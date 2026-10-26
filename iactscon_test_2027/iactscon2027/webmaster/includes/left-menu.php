<?php
include_once("includes/source.php");
include_once('includes/init.php');
include_once('includes/function.workshop.php');

if (!function_exists('h')) {
    function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}

/* URL BASE ----------------------------------------------------------
   URL path of the webmaster root, e.g. /iactscon_test_2027/webmaster/
   Taken from the current request URL (not the filesystem), so it works
   from any page: webmaster/x.php or webmaster/section_quiz/x.php      */
$scriptUrl     = parse_url($_SERVER['SCRIPT_NAME'], PHP_URL_PATH);
$marker        = '/webmaster/';
$pos           = strpos($scriptUrl, $marker);
$webmasterBase = ($pos !== false)
    ? substr($scriptUrl, 0, $pos + strlen($marker))
    : '/iactscon_test_2027/webmaster/'; // fallback

// Or simply hardcode it:
// $webmasterBase = '/iactscon_test_2027/webmaster/';

if (!function_exists('menuUrl')) {
    function menuUrl($path, $file = '')
    {
        global $webmasterBase;

        $path = trim((string)$path);
        $file = trim((string)$file);

        // join folder and file with exactly one slash
        if ($path !== '' && $file !== '') {
            $p = rtrim($path, '/') . '/' . ltrim($file, '/');
        } else {
            $p = $path . $file;
        }

        if ($p === '') return 'javascript:void(null)';

        // already absolute, root-relative, or javascript: leave untouched
        if (preg_match('~^(https?:)?//|^javascript:|^/~i', $p)) return $p;

        return $webmasterBase . ltrim($p, '/');
    }
}

$loggedUserId = $mycms->getLoggedUserId();

/* CONFIG ------------------------------------------------------------ */
// Super admin sees everything. Adjust to your real check.
$isSuperUser = ($loggedUserId == 1);

// Pages every logged-in user may open
$alwaysOpenFiles = array('index.php', 'dashboard.php', 'logout.php', 'profile.php');

// ID ranges used by the Role page for items that have no page row
define('MODULE_ID_OFFSET',  1000000);
define('SECTION_ID_OFFSET', 2000000);

function cleanFile($path)
{
    return strtolower(basename((string)parse_url((string)$path, PHP_URL_PATH)));
}

/* LOGO -------------------------------------------------------------- */
$emailHeader = '';
$sql = array();
$sql['QUERY'] = "SELECT `logo_image` FROM " . _DB_EMAIL_SETTING_ . " WHERE `status`='A' ORDER BY id DESC LIMIT 1";
$sql['PARAM'] = array();
$result = $mycms->sql_select($sql);
if (!empty($result[0]['logo_image'])) {
    $emailHeader = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['logo_image'];
}

/* ACCESS: ids granted to this user ---------------------------------- */
$allowedIds = array();
$sqlAcc = array();
$sqlAcc['QUERY'] = "SELECT DISTINCT pageId FROM " . _DB_CONF_PAGE_ACCESS_ . " WHERE userId = ?";
$sqlAcc['PARAM'][] = array('FILD' => 'userId', 'DATA' => $loggedUserId, 'TYP' => 's');
$resAcc = $mycms->sql_select($sqlAcc);
if ($resAcc) {
    foreach ($resAcc as $r) {
        $allowedIds[(int)$r['pageId']] = true;
    }
}

function canSeePage($id)
{
    global $isSuperUser, $allowedIds;
    return $isSuperUser || isset($allowedIds[(int)$id]);
}

/* DIRECT URL GUARD (registered pages only) -------------------------- */
$registeredFiles = array();
$allowedFiles    = array();
$resAll = $mycms->sql_select(array(
    'QUERY' => "SELECT pageId, fileName FROM " . _DB_WEBMASTER_PAGES_,
    'PARAM' => array()
));
if ($resAll) {
    foreach ($resAll as $r) {
        $f = cleanFile($r['fileName']);
        if ($f === '') continue;
        $registeredFiles[$f] = true;
        if (canSeePage($r['pageId'])) {
            $allowedFiles[$f] = true;
        }
    }
}

$currentFile = strtolower(basename($_SERVER['SCRIPT_NAME']));
if (
    !$isSuperUser
    && !in_array($currentFile, $alwaysOpenFiles, true)
    && isset($registeredFiles[$currentFile])
    && !isset($allowedFiles[$currentFile])
) {
    if (!headers_sent()) {
        http_response_code(403);
    }
    echo '<div style="padding:60px;text-align:center;font-family:sans-serif;">
            <h2>Access Denied</h2>
            <p>You do not have permission to view this page.</p>
            <p><a href="' . h($webmasterBase) . 'dashboard.php">Back to Dashboard</a></p>
          </div>';
    exit();
}
?>
<script src="<?= h($webmasterBase) ?>includes/dynamic_header.js"></script>

<div class="dash_lt">
  <div class="logo_wrap">
    <?php if ($emailHeader !== '') { ?>
      <img src="<?= h($emailHeader) ?>" alt="">
    <?php } ?>
  </div>

  <div id="sidebar" class="menu_wrap">
    <ul id="mainNav" class="main_menu">
      <?php
      $sectionQuery = array();
      $sectionQuery['QUERY'] = "SELECT * FROM " . _DB_WEBMASTER_SECTIONS_ . " WHERE status = ? ORDER BY seq ASC";
      $sectionQuery['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
      $sectionsResult = $mycms->sql_select($sectionQuery);
      if (!$sectionsResult) {
          $sectionsResult = array();
      }

      foreach ($sectionsResult as $section) {
          $sectionId = $section['sectionId'];
          $visibleModules = array();

          $moduleQuery = array();
          $moduleQuery['QUERY'] = "SELECT * FROM " . _DB_WEBMASTER_MODULE_ . " WHERE sectionId = ? ORDER BY seq ASC";
          $moduleQuery['PARAM'][] = array('FILD' => 'sectionId', 'DATA' => $sectionId, 'TYP' => 'i');
          $modules = $mycms->sql_select($moduleQuery);
          if (!$modules) {
              $modules = array();
          }

          foreach ($modules as $module) {
              $moduleKey = MODULE_ID_OFFSET + (int)$module['moduleId'];

              if ($module['is_page'] == 'yes') {
                  // Direct-link module: needs its own checkbox
                  if (!canSeePage($moduleKey)) {
                      continue;
                  }
                  $module['_pages'] = array();
              } else {
                  $pageQuery = array();
                  $pageQuery['QUERY'] = "SELECT * FROM " . _DB_WEBMASTER_PAGES_ . " WHERE moduleId = ? ORDER BY seq ASC";
                  $pageQuery['PARAM'][] = array('FILD' => 'moduleId', 'DATA' => $module['moduleId'], 'TYP' => 'i');
                  $pages = $mycms->sql_select($pageQuery);
                  if (!$pages) {
                      $pages = array();
                  }

                  // Module with no pages at all: shown only if its own checkbox is granted
                  if (empty($pages)) {
                      if (canSeePage($moduleKey)) {
                          $module['_pages'] = array();
                          $visibleModules[] = $module;
                      }
                      continue;
                  }

                  $okPages = array();
                  foreach ($pages as $p) {
                      if (canSeePage($p['pageId'])) {
                          $okPages[] = $p;
                      }
                  }
                  if (empty($okPages)) {
                      continue; // no allowed pages, hide module
                  }
                  $module['_pages'] = $okPages;
              }
              $visibleModules[] = $module;
          }

          // Section with nothing visible shows only if its own checkbox is granted
          if (empty($visibleModules) && !canSeePage(SECTION_ID_OFFSET + (int)$sectionId)) {
              continue;
          }

          if ($section['is_page'] == 'yes') {
              $hrefSec = menuUrl($section['path']);
              $hassub  = '';
          } else {
              $hrefSec = empty($visibleModules) ? menuUrl($section['path']) : 'javascript:void(null)';
              $hassub  = empty($visibleModules) ? '' : 'has-sub';
          }
      ?>
        <li class="main_menu_li nav">
          <a href="<?= h($hrefSec) ?>" class="<?= $hassub ?>">
            <span class="menu_icon"><i class="<?= h($section['img']) ?>"></i></span>
            <span class="menu_txt_wrap">
              <span class="menu_txt"><?= h($section['sectionName']) ?></span>
            </span>
          </a>

          <?php if (!empty($visibleModules)) { ?>
            <ul class="sub_menu">
              <?php foreach ($visibleModules as $module) {
                  $isDirect   = ($module['is_page'] == 'yes');
                  $hrefModule = $isDirect ? menuUrl($module['path']) : 'javascript:void(null)';
                  $hassubSub  = (!$isDirect && !empty($module['_pages'])) ? 'sub-has-sub' : '';
              ?>
                <li class="sub_menu_li">
                  <a href="<?= h($hrefModule) ?>" class="<?= $hassubSub ?>">
                    <span class="menu_txt_wrap">
                      <span class="menu_txt"><?= h($module['moduleName']) ?></span>
                    </span>
                  </a>

                  <?php if (!empty($module['_pages'])) { ?>
                    <ul class="sub_sub_menu">
                      <?php foreach ($module['_pages'] as $page) { ?>
                        <li class="sub_menu_li">
                          <a href="<?= h(menuUrl($page['fileName'])) ?>">
                            <span class="menu_txt_wrap">
                              <span class="menu_txt"><?= h($page['pageName']) ?></span>
                            </span>
                          </a>
                        </li>
                      <?php } ?>
                    </ul>
                  <?php } ?>
                </li>
              <?php } ?>
            </ul>
          <?php } ?>
        </li>
      <?php } ?>
    </ul>
  </div>

  <div class="dash_lt_btm">
    MANAGED BY <img style="width: 88px; margin-left: 6px;" src="https://ruedakolkata.com/natcon_2025/webmaster/rueda_logo.png" alt="">
  </div>
  <button class="slide_left_menu">
    <i class="fal fa-grip-lines-vertical"></i>
  </button>
</div>

<!-- Global Toaster Notifications -->
<div id="successToaster" class="toaster" style="display: none;">
  <div class="toaster_inner badge_success">
    <span class="badge_success">✔</span>
    <p>Data Added Successfully</p>
  </div>
</div>

<div id="errorToaster" class="toaster" style="display: none;">
  <div class="toaster_inner badge_danger">
    <span class="badge_danger">✖</span>
    <p>Data Error</p>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    function showToaster(type, message) {
        var toaster = document.getElementById(type === 'success' ? 'successToaster' : 'errorToaster');
        if (!toaster) return;
        if (message) {
            toaster.querySelector('p').textContent = message;
        }
        toaster.style.display = 'block';
        setTimeout(function () {
            toaster.style.display = 'none';
        }, 5000);
    }

    <?php
    if (!empty($_SESSION['toaster'])) {
        $toasterData = $_SESSION['toaster'];
        unset($_SESSION['toaster']);
    ?>
        showToaster(<?= json_encode($toasterData['type']) ?>, <?= json_encode($toasterData['message']) ?>);
    <?php } ?>
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    function normalize(url) {
        try {
            url = new URL(url, window.location.origin);
        } catch (e) {
            return url;
        }
        return url.pathname.replace(/\/+$/, '').toLowerCase();
    }

    var currentPath = normalize(window.location.href);

    document.querySelectorAll('#mainNav a[href]').forEach(function (link) {
        var href = link.getAttribute('href');
        if (!href || href.indexOf('javascript:') === 0) return;

        if (normalize(link.href) === currentPath) {
            link.classList.add('active');

            var li = link.closest('li');
            while (li && li.closest('#mainNav')) {
                li.classList.add('active', 'open');

                var parentUl = li.parentElement;
                if (parentUl && (parentUl.classList.contains('sub_menu') ||
                                 parentUl.classList.contains('sub_sub_menu'))) {
                    parentUl.style.display = 'block';
                    var parentLink = parentUl.previousElementSibling;
                    if (parentLink && parentLink.tagName === 'A') {
                        parentLink.classList.add('active');
                    }
                }
                li = li.parentElement ? li.parentElement.closest('li') : null;
            }
        }
    });
});
</script>