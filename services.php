<?php

function hookpress_ajax_get_fields() {
  global $wpdb, $hookpress_actions;
  $args = $hookpress_actions[$_POST['hook']];
  $fields = array();
  foreach ($args as $arg) {
    if (ereg('[A-Z]+',$arg))
      $fields = array_merge($fields,hookpress_get_fields($arg));
    else
      $fields[] = $arg;
  }

	header("Content-Type: text/html; charset=UTF-8");

	sort($fields);
	foreach ($fields as $field) {
    echo "<option value='$field'>$field</option>";
  }
  exit;
}

function hookpress_ajax_add_fields() {

  // register the new webhook
  $webhooks = get_option('hookpress_webhooks');
  $newhook = array(
    'url'=>$_POST['url'],
    'hook'=>$_POST['hook'],
    'fields'=>split(',',$_POST['fields']),
    'enabled'=>true
  );
  $webhooks[] = $newhook;
  update_option('hookpress_webhooks',$webhooks);

  // generate the return value
	header("Content-Type: text/html; charset=UTF-8");
  echo hookpress_print_webhook(count($webhooks) - 1);
  exit;
}

function hookpress_ajax_set_enabled() {
  $id = $_POST['id'];
  $enabled = $_POST['enabled'];
  
  // update the webhook
  $webhooks = get_option('hookpress_webhooks');
  $webhooks[$id]['enabled'] = ($enabled == 'true'?true:false);
  update_option('hookpress_webhooks',$webhooks);
  
	header("Content-Type: text/html; charset=UTF-8");
  echo hookpress_print_webhook($id);
  exit;
}

function hookpress_ajax_delete_hook() {
  $webhooks = get_option('hookpress_webhooks');
  $id = $_POST['id'];
  if (!$id)
    die("ERROR: no id given");
  if (!$webhooks[$id])
    die("ERROR: no webhook found for that id");
  $webhooks[$id] = array();
  update_option('hookpress_webhooks', $webhooks);
  echo "ok";
  exit;
}