<?php


function rlrubrica_table_install() {
    global $wpdb;
    global $charset_collate;
    
     
     
    $table_name_rubriload =  $wpdb->prefix . 'rubriload';
    $table_name_lists =  $wpdb->prefix . 'rubriload_lists';
    $table_name_user2list =  $wpdb->prefix . 'rubriload_usertolist';
    
	$sql_rubriload = "
CREATE TABLE IF NOT EXISTS $table_name_utenti (
  `ID` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `flag_controllo` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
	$sql_lists = "
CREATE TABLE IF NOT EXISTS $table_name_lists (
  `IDlist` int(11) NOT NULL auto_increment,
  `List` text NOT NULL,
  PRIMARY KEY  (`IDlist`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
	$sql_user2list = "
CREATE TABLE IF NOT EXISTS $table_name_user2list (
  `IDlist` int(11) NOT NULL,
  `IDuser` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
     
     
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql_rubriload );
    dbDelta( $sql_lists );
    dbDelta( $sql_user2list );
     
     
     
     
}
register_activation_hook(__FILE__,'rlrubrica_table_install');

?>