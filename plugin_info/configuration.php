<?php
/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
  include_file('desktop', '404', 'php');
  die();
}
?>
<form class="form-horizontal">
  <fieldset>
    <div class="form-group">
    </div>
  </fieldset>
</form>

<script>
  document.querySelector("input[data-l1key='functionality::cron::enable']").addEventListener('change', function(e) {
    if (e.target.checked) {
      document.querySelector("input[data-l1key='functionality::cron5::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cron10::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cron15::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cron30::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cronHourly::enable']").removeAttribute('checked');
    }
  });
  document.querySelector("input[data-l1key='functionality::cron5::enable']").addEventListener('change', function(e) {
    if (e.target.checked) {
      document.querySelector("input[data-l1key='functionality::cron::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cron10::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cron15::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cron30::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cronHourly::enable']").removeAttribute('checked');
    }
  });
  document.querySelector("input[data-l1key='functionality::cron10::enable']").addEventListener('change', function(e) {
    if (e.target.checked) {
      document.querySelector("input[data-l1key='functionality::cron::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cron5::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cron15::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cron30::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cronHourly::enable']").removeAttribute('checked');
    }
  });
  document.querySelector("input[data-l1key='functionality::cron15::enable']").addEventListener('change', function(e) {
    if (e.target.checked) {
      document.querySelector("input[data-l1key='functionality::cron::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cron5::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cron10::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cron30::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cronHourly::enable']").removeAttribute('checked');
    }
  });
  document.querySelector("input[data-l1key='functionality::cron30::enable']").addEventListener('change', function(e) {
    if (e.target.checked) {
      document.querySelector("input[data-l1key='functionality::cron::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cron5::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cron10::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cron15::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cronHourly::enable']").removeAttribute('checked');
    }
  });
  document.querySelector("input[data-l1key='functionality::cronHourly::enable']").addEventListener('change', function(e) {
    if (e.target.checked) {
      document.querySelector("input[data-l1key='functionality::cron::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cron5::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cron10::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cron15::enable']").removeAttribute('checked');
      document.querySelector("input[data-l1key='functionality::cron30::enable']").removeAttribute('checked');
    }
  });
</script>