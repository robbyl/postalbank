<?php
/**
 * Copyright (C) 2012-2014 Graham Breach
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * For more information, please contact <graham@goat1000.com>
 */

class SVGGraphJavascript {

  private $settings;
  private $graph;
  protected $functions = array();
  protected $variables = array();
  protected $comments = array();
  protected $onload = FALSE;

  /**
   * Constructor takes array of settings and graph instance as arguments
   */
  public function __construct(&$settings, &$graph)
  {
    $this->settings = $settings;
    $this->graph = $graph;
  }

  /**
   * Return the settings as properties
   */
  public function __get($name)
  {
    $this->{$name} = isset($this->settings[$name]) ? $this->settings[$name] : null;
    return $this->{$name};
  }

  /**
   * Adds a javascript function
   */
  public function AddFunction($name)
  {
    if(isset($this->functions[$name]))
      return TRUE;

    $simple_functions = array(
      'setattr' => "function setattr(i,a,v){i.setAttributeNS(null,a,v);return v}\n",
      'getE' => "function getE(i){return document.getElementById(i)}\n",
      'newtext' => "function newtext(c){return document.createTextNode(c)}\n",
    );

    if(isset($simple_functions[$name])) {
      $this->InsertFunction($name, $simple_functions[$name]);
      return;
    }

    $namespace = $this->namespace ? 'svg:' : '';

    switch($name)
    {
    case 'textFit' :
      $this->AddFunction('setattr');
      $fn = <<<JAVASCRIPT
function textFit(evt,x,y,w,h) {
  var t = evt.target;
  var aw = t.getBBox().width;
  var ah = t.getBBox().height;
  var trans = '';
  var s = 1.0;
  if(aw > w)
    s = w / aw;
  if(s * ah > h)
    s = h / ah;
  if(s != 1.0)
    trans = 'scale(' + s + ') ';
  trans += 'translate(' + (x / s) + ',' + ((y + h) / s) +  ')';
  setattr(t, 'transform', trans);
}\n
JAVASCRIPT;
      break;

    // fadeIn, fadeOut are shortcuts to fader function
    case 'fadeIn' : $name = 'fader';
    case 'fadeOut' : $name = 'fader';
    case 'fader' :
      $this->AddFunction('getE');
      $this->InsertVariable('faders', '', 1); // insert empty object
      $this->InsertVariable('fader_itimer', NULL);
      $fn = <<<JAVASCRIPT
function fadeIn(e,i,s){fader(e,i,0,1,s)}
function fadeOut(e,i,s){fader(e,i,1,0,s)}
function fader(e,i,o1,o2,s) {
  faders[i] = { id: i, o_start: o1, o_end: o2, step: (o1 < o2 ? s : -s) };
  fader_itimer || (fader_itimer = setInterval(fade,50));
}
function fade() {
  var f,ff,t,o;
  for(f in faders) {
    ff = faders[f], t = getE(ff.id);
    o = (t.style.opacity == '' ? ff.o_start : t.style.opacity * 1);
    o += ff.step;
    t.style.opacity = o < .01 ? 0 : (o > .99 ? 1 : o);
    if((ff.step > 0 && o >= 1) || (ff.step < 0 && o <= 0))
      delete faders[f];
  }
}\n
JAVASCRIPT;
      break;

    case 'newel' :
      $this->AddFunction('setattr');
      $fn = <<<JAVASCRIPT
function newel(e,a){
  var ns='http://www.w3.org/2000/svg', ne=document.createElementNS(ns,e),i;
  for(i in a)
    setattr(ne, i, a[i]);
  return ne;
}\n
JAVASCRIPT;
      break;
    case 'showhide' :
      $this->AddFunction('setattr');
      $fn = <<<JAVASCRIPT
function showhide(e,h){setattr(e,'visibility',h?'visible':'hidden');}\n
JAVASCRIPT;
      break;
    case 'finditem' :
      $fn = <<<JAVASCRIPT
function finditem(e,list) {
  var l = e.target.correspondingUseElement || e.target, t;
  while(!t && l.parentNode) {
    t = l.id && list[l.id]
    l = l.parentNode;
  }
  return t;
}\n
JAVASCRIPT;
      break;
    case 'tooltip' :
      $this->AddFunction('getE');
      $this->AddFunction('setattr');
      $this->AddFunction('newel');
      $this->AddFunction('showhide');
      $this->AddFunction('svgNode');
      $this->AddFunction('svgCursorCoords');
      $this->InsertVariable('tooltipOn', '');
      $max_x = $this->graph->width - $this->tooltip_stroke_width;
      $max_y = $this->graph->height - $this->tooltip_stroke_width;
      if(is_numeric($this->tooltip_shadow_opacity)) {
        $ttoffs = (2 - $this->tooltip_stroke_width/2);
        $max_x -= $ttoffs;
        $max_y -= $ttoffs;
        $shadow = <<<JAVASCRIPT
    shadow = newel('rect',{
      fill: '#000',
      opacity: {$this->tooltip_shadow_opacity},
      x:'{$ttoffs}px',y:'{$ttoffs}px',
      width:'10px',height:'10px',
      id: 'ttshdw',
      rx:'{$this->tooltip_round}px',ry:'{$this->tooltip_round}px'
    });
    tt.appendChild(shadow);
JAVASCRIPT;
      } else {
        $shadow = '';
      }
      $dpad = 2 * $this->tooltip_padding;
      $back_colour = $this->graph->ParseColour($this->tooltip_back_colour);
      $fn = <<<JAVASCRIPT
function tooltip(e,callback,on,param) {
  var tt = getE('tooltip'), rect = getE('ttrect'), shadow = getE('ttshdw'),
    offset = {$this->tooltip_offset}, pos = svgCursorCoords(e),
    x = pos[0] + offset, y = pos[1] + offset, inner, brect, bw, bh,
    sw, sh,
    de = svgNode(e);
  if(on && !tt) {
    tt = newel('g',{id:'tooltip',visibility:'visible'});
    rect = newel('rect',{
      stroke: '{$this->tooltip_colour}',
      'stroke-width': '{$this->tooltip_stroke_width}px',
      fill: '{$back_colour}',
      width:'10px',height:'10px',
      id: 'ttrect',
      rx:'{$this->tooltip_round}px',ry:'{$this->tooltip_round}px'
    });
{$shadow}
    tt.appendChild(rect);
  }
  if(tt) {
    if(on) {
      if(tt.parentNode && tt.parentNode != de)
        tt.parentNode.removeChild(tt);
      de.appendChild(tt);
    }
    showhide(tt,on);
  }
  inner = callback(e,tt,on,param);
  if(inner && on) {
    brect = inner.getBBox();
    bw = Math.ceil(brect.width + {$dpad});
    bh = Math.ceil(brect.height + {$dpad});
    setattr(rect, 'width', bw + 'px');
    setattr(rect, 'height', bh + 'px');
    setattr(inner, 'transform', 'translate(' + (bw / 2) + ',0)');
    if(shadow) {
      setattr(shadow, 'width', (bw + {$this->tooltip_stroke_width}) + 'px');
      setattr(shadow, 'height', (bh + {$this->tooltip_stroke_width}) + 'px');
    }
    if(bw + x > {$max_x}) {
      x -= bw + offset * 2;
      x = Math.max(x, 0);
    }
    if(bh + y > {$max_y}) {
      y -= bh + offset * 2;
      y = Math.max(y, 0);
    }
  }
  on && setattr(tt,'transform','translate('+x+' '+y+')');
  tooltipOn = on ? 1 : 0;
}\n
JAVASCRIPT;
      break;

    case 'texttt' :
      $this->AddFunction('getE');
      $this->AddFunction('setattr');
      $this->AddFunction('newel');
      $this->AddFunction('newtext');
      $tty = $this->tooltip_font_size + $this->tooltip_padding;
      $ttypx = "{$tty}px";
      $fn = <<<JAVASCRIPT
function texttt(e,tt,on,t){
  var ttt = getE('tooltiptext'), lines, i, ts, xpos;
  if(on) {
    lines = t.split('\\\\n');
    xpos = '{$this->tooltip_padding}px';
    if(!ttt) {
      ttt = newel('g', {
        id: 'tooltiptext',
        fill: '{$this->tooltip_colour}',
        'font-size': '{$this->tooltip_font_size}px',
        'font-family': '{$this->tooltip_font}',
        'font-weight': '{$this->tooltip_font_weight}',
        'text-anchor': 'middle'
      });
      tt.appendChild(ttt);
    }
    while(ttt.childNodes.length > 0)
      ttt.removeChild(ttt.childNodes[0]);
    for(i = 0; i < lines.length; ++i) {
      ts = newel('text', { y: ({$tty} * (i + 1)) + 'px' });
      ts.appendChild(newtext(lines[i]));
      ttt.appendChild(ts);
    }
  }
  ttt && showhide(ttt,on);
  return ttt;
}\n
JAVASCRIPT;
      break;
    case 'ttEvent' :
      $this->AddFunction('finditem');
      $this->AddFunction('init');
      $this->InsertVariable('initfns', NULL, 'ttEvt');
      $fn = <<<JAVASCRIPT
function ttEvt() {
  document.addEventListener && document.addEventListener('mousemove',
    function(e) {
      var t = finditem(e,tips);
      if(t || tooltipOn)
        tooltip(e,texttt,t,t);
    },false);
}\n
JAVASCRIPT;
      break;
    case 'fadeEvent' :
      $this->AddFunction('getE');
      $this->AddFunction('init');
      $this->InsertVariable('initfns', NULL, 'fade');
      $fn = <<<JAVASCRIPT
function fade() {
  var f,f1,e,o;
  for(f in fades) {
    f1 = fades[f];
    if(f1.dir) {
      e = getE(f1.id);
      o = (e.style.opacity || fstart) * 1 + f1.dir;
      e.style.opacity = o < .01 ? 0 : (o > .99 ? 1 : o);
    }
  }
  setTimeout(fade,50);
}\n
JAVASCRIPT;
      break;
    case 'fadeEventIn' :
      $this->AddFunction('init');
      $this->AddFunction('finditem');
      $this->InsertVariable('initfns', NULL, 'fiEvt');
      $fn = <<<JAVASCRIPT
function fiEvt() {
  var f;
  for(f in fades)
    getE(fades[f].id).style.opacity = fstart;
  document.addEventListener && document.addEventListener('mouseover',
    function(e) {
      var t = finditem(e,fades);
      t && (t.dir = fistep);
    },false);
}\n
JAVASCRIPT;
      break;
    case 'fadeEventOut' :
      $this->AddFunction('init');
      $this->AddFunction('finditem');
      $this->InsertVariable('initfns', NULL, 'foEvt');
      $fn = <<<JAVASCRIPT
function foEvt() {
  document.addEventListener && document.addEventListener('mouseout',
    function(e) {
      var t = finditem(e,fades);
      t && (t.dir = fostep);
    },false);
}\n
JAVASCRIPT;
      break;
    case 'duplicate' :
      $this->AddFunction('getE');
      $this->AddFunction('newel');
      $this->AddFunction('init');
      $this->InsertVariable('initfns', NULL, 'initDups');
      $fn = <<<JAVASCRIPT
function duplicate(f,t) {
  var e = getE(f), g, a, p = e && e.parentNode;
  if(e) {
    while(p.parentNode && p.nodeName != '{$namespace}svg' &&
      (p.nodeName != '{$namespace}g' || !p.getAttributeNS(null,'clip-path'))) {
      p.nodeName == '{$namespace}a' && (a = p);
      p = p.parentNode;
    }
    g = e.cloneNode(true);
    g.style.opacity = 0;
    e.id = t;

    if(a) {
      a = a.cloneNode(false);
      a.appendChild(g);
      g = a;
    }
    p.appendChild(g);
  }
}
function initDups() {
  for(var d in dups)
    duplicate(d,dups[d]);
}\n
JAVASCRIPT;
      break;
    case 'svgNode' :
      $fn = <<<JAVASCRIPT
function svgNode(e) {
  var d = e.target.correspondingUseElement || e.target;
  while(d.parentNode && d.nodeName != '{$namespace}svg')
    d = d.parentNode;
  return d
}\n
JAVASCRIPT;
      break;
    case 'svgCursorCoords' :
      $this->AddFunction('svgNode');
      $fn = <<<JAVASCRIPT
function svgCursorCoords(e) {
  var d = svgNode(e);
  if (!d.createSVGPoint || !d.getScreenCTM) {
    return [e.clientX,e.clientY];
  }
  var pt = d.createSVGPoint(); pt.x = e.clientX; pt.y = e.clientY;
  pt = pt.matrixTransform(d.getScreenCTM().inverse());
  return [pt.x,pt.y];
}\n
JAVASCRIPT;
      break;
    case 'autoHide' :
      $this->AddFunction('init');
      $this->AddFunction('getE');
      $this->AddFunction('setattr');
      $this->InsertVariable('initfns', NULL, 'autoHide');
      $fn = <<<JAVASCRIPT
function autoHide() {
  if(document.addEventListener) {
    for(var a in autohide)
      autohide[a] = getE(a);
    document.addEventListener('mouseout', function(e) {
      setattr(finditem(e,autohide),'opacity',1);
    });
    document.addEventListener('mouseover', function(e) {
      setattr(finditem(e,autohide),'opacity',0);
    });
  }
}\n
JAVASCRIPT;
      break;
    case 'chEvt' :
      $this->AddFunction('init');
      $this->InsertVariable('initfns', NULL, 'chEvt');
      $fn = <<<JAVASCRIPT
function chEvt() {
  document.addEventListener && document.addEventListener('mousemove',
    crosshairs, false);
}\n
JAVASCRIPT;
      break;
    case 'getData' :
      $fn = <<<JAVASCRIPT
function getData(doc,ename) {
  var ns = 'http://www.goat1000.com/svggraph', element;
  element = doc.getElementsByTagName('svggraph:' + ename);
  if(!element.length)
    element = doc.getElementsByTagNameNS(ns, ename);
  if(!element.length)
    return null;
  return element[0];
}\n
JAVASCRIPT;
      break;
    case 'fitRect' :
      $this->AddFunction('setattr');
      $fn = <<<JAVASCRIPT
function fitRect(rect,brect,pad) {
  var bw = Math.ceil(brect.width + pad + pad),
    bh = Math.ceil(brect.height + pad + pad);
  setattr(rect, 'x', (brect.x - pad) + 'px');
  setattr(rect, 'y', (brect.y - pad) + 'px');
  setattr(rect, 'width', bw + 'px');
  setattr(rect, 'height', bh + 'px');
}\n
JAVASCRIPT;
      break;
    case 'textAttr' :
      $fn = <<<JAVASCRIPT
function textAttr(e,a) {
  var s = e.getAttributeNS(null,a);
  return s ? s : '';
}\n
JAVASCRIPT;
      break;
    case 'showCoords' :
      $this->AddFunction('getE');
      $this->AddFunction('newel');
      $this->AddFunction('newtext');
      $this->AddFunction('getData');
      $this->AddFunction('showhide');
      $this->AddFunction('fitRect');
      $this->AddFunction('textAttr');
      $yb = "textAttr(ti,'unitsby') + ";
      $ya = " + textAttr(ti,'unitsy')";
      $xb = "textAttr(ti,'unitsbx') + ";
      $xa = " + textAttr(ti,'unitsx')";
      $text_format = "{$xb}x1.toFixed(xp){$xa} + ', ' + {$yb}y1.toFixed(yp){$ya}";
      if(!$this->crosshairs_show_h)
        $text_format = "{$xb}x1.toFixed(xp){$xa}";
      elseif(!$this->crosshairs_show_v)
        $text_format = "{$yb}y1.toFixed(yp){$ya}";
      $font_size = max(3, (int)$this->crosshairs_text_font_size);
      $pad = max(0, (int)$this->crosshairs_text_padding);
      $space = max(0, (int)$this->crosshairs_text_space);
      $fn = <<<JAVASCRIPT
function showCoords(de,x,y,bb,on) {
  var gx = getData(de, 'gridx'), gy = getData(de, 'gridy'),
    textList = getData(de,'chtext'), group, i, x1, y1, xz, yz, xp, yp,
    textNode, rect, gbb, tbb, ti, ds, ybase, xbase, lgmin, lgmax, lgmul;
  for(i = 0; i < textList.childNodes.length; ++i) {
    if(textList.childNodes[i].nodeName == 'svggraph:chtextitem') {
      ti = textList.childNodes[i];
      group = getE(ti.getAttributeNS(null, 'groupid'));
      if(on) {
        textNode = group.querySelector('text');
        rect = group.querySelector('rect');
        while(textNode.childNodes.length > 0)
          textNode.removeChild(textNode.childNodes[0]);
        xz = gx.getAttributeNS(null, 'zero');
        yz = gy.getAttributeNS(null, 'zero');
        xp = gx.getAttributeNS(null, 'precision');
        yp = gy.getAttributeNS(null, 'precision');
        xbase = gx.getAttributeNS(null, 'base');
        ybase = gy.getAttributeNS(null, 'base');
        gbb = group.getBBox();
        if(xbase) {
          lgmin = Math.log(xz)/Math.log(xbase);
          lgmax = Math.log(gx.getAttributeNS(null, 'scale'))/Math.log(xbase);
          lgmul = bb.width / (lgmax - lgmin);
          x1 = Math.pow(xbase, lgmin*1 + x / lgmul);
        } else {
          x1 = (x - xz) / gx.getAttributeNS(null, 'scale');
        }
        if(ybase) {
          lgmin = Math.log(yz)/Math.log(ybase);
          lgmax = Math.log(gy.getAttributeNS(null, 'scale'))/Math.log(ybase);
          lgmul = bb.height / (lgmax - lgmin);
          y1 = Math.pow(ybase, lgmin*1 + (bb.height - y) / lgmul);
        } else {
          y1 = (bb.height - y - yz) / gy.getAttributeNS(null, 'scale');
        }
        textNode.appendChild(newtext({$text_format}));
        setattr(textNode, 'y', 0 + 'px');
        tbb = textNode.getBBox();
        ds = tbb.height + tbb.y;
        x1 = x + bb.x + {$pad} + {$space};
        y1 = y + bb.y - {$pad} - {$space} - ds;
        if(x1 + tbb.width + {$pad} > bb.x + bb.width)
          x1 -= gbb.width + ({$space} * 2);
        if(y1 - tbb.height - {$pad} < bb.y)
          y1 += gbb.height + ({$space} * 2);
        setattr(textNode, 'x', x1 + 'px');
        setattr(textNode, 'y', y1 + 'px');
        tbb = textNode.getBBox();
        fitRect(rect,tbb,{$pad});
      }
      showhide(group, on);
    }
  }
}\n
JAVASCRIPT;
      break;
    case 'crosshairs' :
      $this->AddFunction('chEvt');
      $this->AddFunction('setattr');
      $this->AddFunction('svgNode');
      $this->AddFunction('svgCursorCoords');
      $this->AddFunction('showhide');
      $show_text = '';
      if($this->crosshairs_show_text) {
        $this->AddFunction('showCoords');
        $show_text = "showCoords(de, x - bb.x, y - bb.y, bb, on);";
      }
      $show_x = $this->crosshairs_show_h ? 'showhide(xc, on);' : '';
      $show_y = $this->crosshairs_show_v ? 'showhide(yc, on);' : '';
      $fn = <<<JAVASCRIPT
function crosshairs(e) {
  var de = svgNode(e), pos = svgCursorCoords(e), xc = de.querySelector('.chX'),
    yc = de.querySelector('.chY'), grid = de.querySelector('.grid'),
    bb = grid.getBBox(), x = pos[0], y = pos[1],
  on = (x >= bb.x && x <= bb.x + bb.width && y >= bb.y && y <= bb.y + bb.height);
  if(on) {
    setattr(xc,'y1',setattr(xc,'y2', y));
    setattr(yc,'x1',setattr(yc,'x2', x));
  }
  {$show_text}
  {$show_x}
  {$show_y}
}\n
JAVASCRIPT;
      break;
    case 'dragOver' :
      $this->AddFunction('getE');
      $this->AddFunction('setattr');
      $fn = <<<JAVASCRIPT
function dragOver(e,el) {
  var t = getE(el), d;
  if(t && t.dragging) {
    d = t.draginfo;
    var pos = svgCursorCoords(e);
    d[2] = d[2] - d[0] + pos[0];
    d[3] = d[3] - d[1] + pos[1];
    d[0] = pos[0];
    d[1] = pos[1];
    setattr(d[4], 'transform', 'translate(' + d[2] + ',' + d[3] + ')');
    return false;
  }
}\n
JAVASCRIPT;
      break;
    case 'dragStart' :
      $this->AddFunction('getE');
      $this->AddFunction('newel');
      $fn = <<<JAVASCRIPT
function dragStart(e,el) {
  var t = getE(el), m;
  var pos = svgCursorCoords(e);
  if(!t.draginfo) {
    t.draginfo = [0,0,0,0,newel('g',{cursor:'move'})];
    t.parentNode.appendChild(t.draginfo[4]);
    t.parentNode.removeChild(t);
    t.draginfo[4].appendChild(t);
  }
  t.draginfo[0] = pos[0];
  t.draginfo[1] = pos[1];

  t.dragging = 1;
  return false;
}\n
JAVASCRIPT;
      break;
    case 'dragEnd' :
      $this->AddFunction('getE');
      $fn = <<<JAVASCRIPT
function dragEnd(e,el) {
  getE(el).dragging = null;
}\n
JAVASCRIPT;
      break;
    case 'dragEvent' :
      $this->AddFunction('init');
      $this->AddFunction('newel');
      $this->AddFunction('getE');
      $this->AddFunction('setattr');
      $this->AddFunction('finditem');
      $this->InsertVariable('initfns', NULL, 'initDrag');
      $fn = <<<JAVASCRIPT
function initDrag() {
  var d, e;
  if(document.addEventListener) {
    for(d in draggable) {
      e = draggable[d] = getE(d);
      e.draginfo = [0,0,0,0,newel('g',{cursor:'move'})];
      (e.nearestViewportElement || document.documentElement).appendChild(e.draginfo[4]);
      e.parentNode.removeChild(e);
      e.draginfo[4].appendChild(e);
    }
    document.addEventListener('mouseup', function(e) {
      var t = finditem(e,draggable);
      if(t && t.dragging) {
        t.dragging = null;
      }
    });
    document.addEventListener('mousedown', function(e) {
      var t = finditem(e,draggable), m;
      if(t && !t.dragging) {
        var pos = svgCursorCoords(e);
        t.draginfo[0] = pos[0];
        t.draginfo[1] = pos[1];
        t.dragging = 1;
        e.cancelBubble = true;
        e.preventDefault && e.preventDefault();
        return false;
      }
    });
    function dragmove(e) {
      var t = finditem(e,draggable), d;
      if(t && t.dragging) {
        d = t.draginfo;
        var pos = svgCursorCoords(e);
        d[2] = d[2] - d[0] + pos[0];
        d[3] = d[3] - d[1] + pos[1];
        d[0] = pos[0];
        d[1] = pos[1];
        setattr(d[4], 'transform', 'translate(' + d[2] + ',' + d[3] + ')');
        e.cancelBubble = true;
        e.preventDefault && e.preventDefault();
        return false;
      }
    };
    document.addEventListener('mousemove', dragmove);
    document.addEventListener('mouseout', dragmove);
  }
}\n
JAVASCRIPT;
      break;
    case 'init' :
      $this->onload = TRUE;
      $fn = <<<JAVASCRIPT
function init() {
  if(!document.addEventListener || !initfns)
    return;
  for(var f in initfns)
    eval(initfns[f] + '()');
}\n
JAVASCRIPT;
      break;

    default :
      // Trying to add a function that doesn't exist?
      throw new Exception("Unknown function '$name'");
    }

    $this->InsertFunction($name, $fn);
  }

  /**
   * Inserts a Javascript function into the list
   */
  public function InsertFunction($name, $fn)
  {
    $this->functions[$name] = $fn;
  }

  public static function hex_callback($m)
  {
    return self::dec2utf8(hexdec($m[1]));
  }

  public static function dec_callback($m)
  {
    return self::dec2utf8($m[1]);
  }

  /**
   * Returns the utf8 string corresponding to the unicode value
   * (from php.net, courtesy - romans@void.lv)
   */
  public static function dec2utf8($num)
  {
    if ($num < 128) {
      return chr($num);
    }
    if ($num < 2048) {
      return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
    }
    if ($num < 65536) {
      return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
    }
    if ($num < 2097152) {
      return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
    }
    return '';
  }

  /**
   * Convert XML char entities to unicode.
   */
  public static function DeEntity($string)
  {
    if (!is_string($string) or is_numeric($string)) {
      return $string;
    }

    $string = preg_replace_callback('/&#x([a-f0-9]+);/',
      'SVGGraphJavascript::hex_callback', $string);
    $string = preg_replace_callback('/&#([0-9]+);/',
      'SVGGraphJavascript::dec_callback', $string);
    return $string;
  }

  /**
   * Adds a Javascript variable
   * - use $value:$more for assoc
   * - use NULL:$more for array
   */
  public function InsertVariable($var, $value, $more = NULL)
  {
    if(is_null($more))
      $this->variables[$var] = $this->DeEntity($value);
    elseif(is_null($value))
      $this->variables[$var][] = $this->DeEntity($more);
    else
      $this->variables[$var][$value] = $this->DeEntity($more);
  }

  /**
   * Insert a comment into the Javascript section - handy for debugging!
   */
  public function InsertComment($details)
  {
    $this->comments[] = $details;
  }

  /**
   * Adds an inline event handler to an element's array
   */
  public function AddEventHandler(&$array, $evt, $code)
  {
    if(isset($array[$evt]))
      $array[$evt] .= ';' . $code;
    else
      $array[$evt] = $code;
  }

  /**
   * Sets the tooltip for an element
   */
  public function SetTooltip(&$element, $text, $duplicate = FALSE)
  {
    $this->AddFunction('tooltip');
    $this->AddFunction('texttt');
    if($this->compat_events) {
      $this->AddEventHandler($element, 'onmousemove',
        "tooltip(evt,texttt,true," . self::json_encode($text) . ')');
      $this->AddEventHandler($element, 'onmouseout',
        "tooltip(evt,texttt,false,'')");
    } else {
      if(!isset($element['id']))
        $element['id'] = $this->graph->NewID();
      $this->AddFunction('ttEvent');
      $this->InsertVariable('tips', $element['id'], $text);
    }
    if($duplicate) {
      if(!isset($element['id']))
        $element['id'] = $this->graph->NewID();
      $this->AddOverlay($element['id'], $this->graph->NewID());
    }
  }

  /**
   * Sets the fader for an element
   */
  public function SetFader(&$element, $in, $out, $target = NULL,
    $duplicate = FALSE)
  {
    if(!isset($element['id']))
      $element['id'] = $this->graph->NewID();
    if(is_null($target))
      $target = $element['id'];
    $id = $duplicate ? $this->graph->NewID() : $element['id'];
    if($this->compat_events) {
      if($in) {
        $this->AddFunction('fadeIn');
        $this->AddEventHandler($element, 'onmouseover',
          'fadeIn(evt,"' . $target . '", ' . $in . ')');
      }
      if($out) {
        $this->AddFunction('fadeOut');
        $this->AddEventHandler($element, 'onmouseout',
          'fadeOut(evt,"' . $target . '", ' . $out . ')');
      }
    } else {

      $this->AddFunction('fadeEvent');
      if($in) {
        $this->AddFunction('fadeEventIn');
        $this->InsertVariable('fistep', $in, NULL);
      }
      if($out) {
        $this->AddFunction('fadeEventOut');
        $this->InsertVariable('fostep', -$out, NULL);
      }
      $this->InsertVariable('fades', $element['id'],
        array('id' => $target, 'dir' => 0));
      $this->InsertVariable('fstart', $in ? 0 : 1, NULL);
    }
    if($duplicate)
      $this->AddOverlay($element['id'], $id);
  }

  /**
   * Makes an item draggable
   */
  public function SetDraggable(&$element)
  {
    if(!isset($element['id']))
      $element['id'] = $this->graph->NewID();
    if($this->compat_events) {
      $this->AddFunction('dragOver');
      $this->AddFunction('dragStart');
      $this->AddFunction('dragEnd');
      $this->AddFunction('svgCursorCoords');
      $this->AddEventHandler($element, 'onmousemove',
        "dragOver(evt,'$element[id]')");
      $this->AddEventHandler($element, 'onmousedown',
        "dragStart(evt,'$element[id]')");
      $this->AddEventHandler($element, 'onmouseup',
        "dragEnd(evt,'$element[id]')");
    } else {
      $this->AddFunction('dragEvent');
      $this->InsertVariable('draggable', $element['id'], 0);
    }
  }

  /**
   * Makes something auto-hide
   */
  public function AutoHide(&$element)
  {
    if(!isset($element['id']))
      $element['id'] = $this->graph->NewID();
    if($this->compat_events) {
      $this->AddFunction('setattr');
      $this->AddFunction('getE');
      $this->AddEventHandler($element, 'onmouseover',
        "setattr(getE('$element[id]'),'opacity',0)");
      $this->AddEventHandler($element, 'onmouseout',
        "setattr(getE('$element[id]'),'opacity',1)");
    } else {
      $this->AddFunction('autoHide');
      $this->InsertVariable('autohide', $element['id'], 0);
    }
  }

  /**
   * Add an overlaid copy of an element, with opacity of 0
   */
  public function AddOverlay($from, $to)
  {
    $this->AddFunction('duplicate');
    $this->InsertVariable('dups', $from, $to);
  }

  /**
   * Wrapper for standard json_encode or emulation method.
   * @param mixed $value
   * @return string
   */
  public static function json_encode($value)
  {
    if (function_exists('json_encode')) {
      return json_encode($value);
    } else {
      return self::emulate_json_encode($value);
    }
  }

  /**
   * Emulate json_encode in legacy PHP versions.
   *
   * NOTE: do not use directly, use self::json_encode() instead.
   *
   * @param mixed $value
   * @return string
   */
  public static function emulate_json_encode($value)
  {
    // This is based on code from http://php.net/manual/en/function.json-encode.php#78719

    if (is_null($value)) {
      return 'null';
    }
    if ($value === false) {
      return 'false';
    }
    if ($value === true) {
      return 'true';
    }
    if (is_integer($value)) {
      return strval($value);
    }
    if (is_float($value)) {
      // Always use "." for floats.
      return str_replace(",", ".", strval($value));
    }
    if (is_scalar($value)) {
      $value = strval($value);
      static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\x08", "\f", '"', "\x00"), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"', '\u0000'));
      return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $value) . '"';
    }

    if (is_object($value)) {
      $isList = false;
    } else {
      $isList = true;
      $i = 0;
      foreach ($value as $k => $v) {
        if ($k !== $i) {
          $isList = false;
          break;
        }
        $i++;
      }
    }

    $result = array();

    if ($isList) {
      foreach ($value as $v) {
        $result[] = self::emulate_json_encode($v);
      }
      return '[' . join(',', $result) . ']';
    }

    foreach ($value as $k => $v) {
      $key = self::emulate_json_encode(strval($k));
      $key = '"'.trim($key, '"').'"';
      $result[] = $key.':'.self::emulate_json_encode($v);
    }
    return '{' . join(',', $result) . '}';
  }

  /**
   * Returns the variables (and comments) as Javascript code
   */
  public function GetVariables()
  {
    $variables = '';
    if(count($this->variables)) {
      $vlist = array();
      foreach($this->variables as $name => $value) {
        $vlist[] = $name . '=' . self::json_encode($value);
      }
      $variables = "var " . implode(', ', $vlist) . ";";
    }
    // comments can be stuck with the variables
    if(count($this->comments)) {
      foreach($this->comments as $c) {
        if(!is_string($c))
          $c = print_r($c, TRUE);
        $variables .= "\n// " . str_replace("\n", "\n// ", $c);
      }
    }
    return $variables;
  }


  /**
   * Returns the functions as Javascript code
   */
  public function GetFunctions()
  {
    $functions = '';
    if(count($this->functions))
      $functions = implode('', $this->functions);
    return $functions;
  }

  /**
   * Returns the onload code to use for the SVG
   */
  public function GetOnload()
  {
    return $this->onload ? 'init()' : '';
  }

}

