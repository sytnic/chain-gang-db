<?php require_once('../../private/initialize.php'); ?>

<?php
//  если пользователь не вошёл, редиректить его
if(!$session->is_logged_in()) {
    redirect_to(url_for('/staff/login.php'));
} else {
    // Do nothing, let the rest of the page proceed  
}
?>

<?php $page_title = 'Staff Menu'; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="content">
  <div id="main-menu">
    <h2>Main Menu</h2>
    <ul>
      <li><a href="<?php echo url_for('/staff/bicycles/index.php'); ?>">Bicycles</a></li>
      <li><a href="<?php echo url_for('/staff/admins/index.php'); ?>">Admins</a></li>
    </ul>
  </div>

</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>
