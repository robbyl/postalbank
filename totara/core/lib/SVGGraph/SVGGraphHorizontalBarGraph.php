<?php
/**
 * Copyright (C) 2011-2014 Graham Breach
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

require_once 'SVGGraphGridGraph.php';

class HorizontalBarGraph extends GridGraph {

  protected $flip_axes = true;
  protected $label_centre = true;
  protected $legend_reverse = true;
  protected $bar_styles = array();

  protected function Draw()
  {
    $body = $this->Grid() . $this->Guidelines(SVGG_GUIDELINE_BELOW);
    $bar_height = $this->BarHeight();
    $bspace = max(0, ($this->y_axes[$this->main_y_axis]->Unit() - $bar_height) / 2);

    $bnum = 0;
    $ccount = count($this->colours);
    foreach($this->values[0] as $item) {
      $bar = array('height' => $bar_height);
      $bar_pos = $this->GridPosition($item->key, $bnum);
      if($this->legend_show_empty || $item->value != 0) {
        $bar_style = array('fill' => $this->GetColour($item, $bnum % $ccount));
        $this->SetStroke($bar_style, $item);
      } else {
        $bar_style = NULL;
      }

      if(!is_null($item->value) && !is_null($bar_pos)) {
        $bar['y'] = $bar_pos - $bspace - $bar_height;
        $this->Bar($item->value, $bar);

        if($bar['width'] > 0) {
          if($this->show_tooltips)
            $this->SetTooltip($bar, $item, $item->value, null,
              !$this->compat_events && $this->show_bar_labels);
          $rect = $this->Element('rect', $bar, $bar_style);
          if($this->show_bar_labels)
            $rect .= $this->BarLabel($item, $bar);
          $body .= $this->GetLink($item, $item->key, $rect);
        }
      }
      $this->bar_styles[] = $bar_style;
      ++$bnum;
    }

    $body .= $this->Guidelines(SVGG_GUIDELINE_ABOVE) . $this->Axes();
    return $body;
  }

  /**
   * Returns the height of a bar rectangle
   */
  protected function BarHeight()
  {
    if(is_numeric($this->bar_width) && $this->bar_width >= 1)
      return $this->bar_width;
    $unit_h = $this->y_axes[$this->main_y_axis]->Unit();
    return $this->bar_space >= $unit_h ? '1' : $unit_h - $this->bar_space;
  }

  /**
   * Fills in the x-position and width of a bar
   * @param number $value bar value
   * @param array  &$bar  bar element array [out]
   * @param number $start bar start value
   * @return number unclamped bar position
   */
  protected function Bar($value, &$bar, $start = null)
  {
    if($start)
      $value += $start;

    $startpos = is_null($start) ? $this->OriginX() : $this->GridX($start);
    if(is_null($startpos))
      $startpos = $this->OriginX();
    $pos = $this->GridX($value);
    if(is_null($pos)) {
      $bar['width'] = 0;
    } else {
      $l1 = $this->ClampHorizontal($startpos);
      $l2 = $this->ClampHorizontal($pos);
      $bar['x'] = min($l1, $l2);
      $bar['width'] = abs($l1-$l2);
    }
    return $pos;
  }

  /**
   * Returns the position for a bar label
   */
  protected function BarLabelPosition(&$item, &$bar)
  {
    $content = $item->Data('label');
    if(is_null($content))
      $content = $this->units_before_label . Graph::NumString($item->value) .
        $this->units_label;
    list($text_size) = $this->TextSize($content, $this->bar_label_font_size,
      $this->bar_label_font_adjust, $this->encoding);

    $pos = $this->bar_label_position;
    if(empty($pos))
      $pos = 'top';
    $top = $bar['x'] + $bar['width'] - $this->bar_label_space;
    $bottom = $bar['x'] + $this->bar_label_space;

    if($top - $text_size < $bottom)
      $pos = 'above';
    return $pos;
  }

  /**
   * Text labels in or above the bar
   */
  protected function BarLabel(&$item, &$bar, $offset_x = null)
  {
    $content = $item->Data('label');
    if(is_null($content))
      $content = $this->units_before_label . Graph::NumString($item->value) .
        $this->units_label;
    $font_size = $this->bar_label_font_size;
    $y = $bar['y'] + ($bar['height'] + $font_size) / 2 - $font_size / 8;
    $colour = $this->bar_label_colour;
    $acolour = $this->bar_label_colour_above;
    $anchor = 'end';

    if(!is_null($offset_x)) {
      $x = $bar['x'] + $bar['width'] - $offset_x;
      $anchor = 'start';
    } else {
      $pos = $this->BarLabelPosition($item, $bar);
      $top = $bar['x'] + $bar['width'] - $this->bar_label_space;
      $bottom = $bar['x'] + $this->bar_label_space;

      $swap = ($bar['x'] + $bar['width'] <= $this->pad_left + 
        $this->x_axes[$this->main_x_axis]->Zero());
      switch($pos) {
      case 'above' :
        $x = $swap ? $bottom - $this->bar_label_space * 2 :
          $top + $this->bar_label_space * 2;
        $anchor = $swap ? 'end' : 'start';
        if(!empty($acolour))
          $colour = $acolour;
        break;
      case 'bottom' :
        $x = $swap ? $top : $bottom;
        $anchor = $swap ? 'end' : 'start';
        break;
      case 'centre' :
        $x = $bar['x'] + $bar['width'] / 2;
        $anchor = 'middle';
        break;
      case 'top' :
      default :
        $x = $swap ? $bottom : $top;
        $anchor = $swap ? 'start' : 'end';
        break;
      }
    }

    $text = array(
      'x' => $x,
      'y' => $y,
      'text-anchor' => $anchor,
      'font-family' => $this->bar_label_font,
      'font-size' => $font_size,
      'fill' => $colour,
    );
    if($this->bar_label_font_weight != 'normal')
      $text['font-weight'] = $this->bar_label_font_weight;
    return $this->Element('text', $text, NULL, $content);
  }

  /**
   * Return box for legend
   */
  protected function DrawLegendEntry($set, $x, $y, $w, $h)
  {
    if(!isset($this->bar_styles[$set]))
      return '';

    $bar = array('x' => $x, 'y' => $y, 'width' => $w, 'height' => $h);
    return $this->Element('rect', $bar, $this->bar_styles[$set]);
  }

}

