<?php 
/*
 * This file is part of Compost.
 *
 * Compost is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Compost is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Compost.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class relativedate {
		function relativedate() {
			
		}
    
    function getRelativeDate($time) {
      $today = strtotime(date('M j, Y'));
      $reldays = ($time - $today)/86400;
      if ($reldays >= 0 && $reldays < 1) {
          return 'today';
      } else if ($reldays >= 1 && $reldays < 2) {
          return 'tomorrow';
      } else if ($reldays >= -1 && $reldays < 0) {
          return 'yesterday';
      }
      if (abs($reldays) < 7) {
          if ($reldays > 0) {
              $reldays = floor($reldays);
              return 'in ' . $reldays . ' day' . ($reldays != 1 ? 's' : '');
          } else {
              $reldays = abs(floor($reldays));
              return $reldays . ' day'  . ($reldays != 1 ? 's' : '') . ' ago';
          }
      }
      if (abs($reldays) < 182) {
          return date('l, F j',$time ? $time : time());
      } else {
          return date('l, F j, Y',$time ? $time : time());
      }
    }
		
    
}
?>