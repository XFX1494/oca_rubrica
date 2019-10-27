<?php
/**
 * @package Rubrica Load
 * @version 1.2
 */
/*
Plugin Name: Rubrica Load
Plugin URI: http://www.contradadelloca.it/
Description: Questo plugin gestisce il caricamento della rubrica
Author: Gianluca Failli
Version: 1.2
Author URI: 
*/

include_once("rubrica_config.php");
include_once("rubrica_install.php");

function rubriload_admin_actions() {  // Inizializzazione del pannello di controllo

	// creo il menu
//	add_menu_page('Rubrica', 'Rubrica', 8, basename(__FILE__), 'rlrubrica_load');
	add_menu_page('Rubrica','Rubrica', 'manage_options', 'rubrica_load.php', 'rubrica_listing_page');
	add_submenu_page('rubrica_load.php', 'Rubrica', 'Rubrica', 'manage_options',   'rubrica_load.php'); // replica il add_menu_page() per avere nome di menu personalizzato
	
	add_submenu_page(basename(__FILE__), 'Aggiorna', 'Aggiorna', 8, 'rlrubrica_import_page', 'rlrubrica_import_page');
	
	add_submenu_page(basename(__FILE__), 'Impostazioni', 'Impostazioni', 8, 'rlrubrica_settings_page', 'rlrubrica_settings_page');
	add_action( 'admin_init', 'register_rlrubrica_settings' );
}
add_action('admin_menu', 'rubriload_admin_actions');



function rubrica_listing_page(){
    global $wpdb;
    $urlpage = "admin.php?page=rubrica_load.php";
    
    if($_POST["lista"] != ''){
        
	    $sql = "SELECT id, name, email FROM " . $wpdb->prefix . "rubriload as R, " . $wpdb->prefix . "rubriload_usertolist as U
	            WHERE R.id = U.IDuser and U.IDlist = " . $_POST["lista"];
        
    }else{
    
    
	    $sql = "SELECT id, name, email FROM " . $wpdb->prefix . "rubriload";
    }

	$utenti = $wpdb->get_results($sql);
?>


    <div class="wrap">
        <h1 class="wp-heading-inline">Lista degli utenti</h1>

		<form action="<?=$urlpage;?>" method="post" style='float: right;'>
            <select name='lista'>
                <option value=''>Generale</option>
<?php
	$sql = "SELECT IDlist, List FROM " . $wpdb->prefix . "rubriload_lists";

	$opliste = $wpdb->get_results($sql);
        if(is_array($opliste)) :
        foreach ($opliste as $l) :
            echo "<option value='" . $l->IDlist . "'";
                if($l->IDlist == $_POST["lista"]){
                    echo " selected='true' ";
                }
            echo ">" . $l->List . "</option>";
		endforeach;
		endif;
?>
            </select><button class="button button-primary">OK</button>
            </form>
<table class="widefat fixed" cellspacing="0">
    <thead>
    <tr>
 
            <th id="codice" class="manage-column column-codice" scope="col">Codice</th>
            <th id="nome" class="manage-column column-nome" scope="col">Nome</th>
            <th id="email" class="manage-column column-email" scope="col">Email</th>
            <th id="liste" class="manage-column column-liste" scope="col">Liste</th>

    </tr>
    </thead>

    <tfoot>
    <tr>
            <th id="codice" class="manage-column column-codice" scope="col">Codice</th>
            <th id="nome" class="manage-column column-nome" scope="col">Nome</th>
            <th id="email" class="manage-column column-email" scope="col">Email</th>
            <th id="liste" class="manage-column column-liste" scope="col">Liste</th>

    </tr>
    </tfoot>
    		
    		
        <?php
//        print_r($utenti);
$t = 0;
if(is_array($utenti)) :
        foreach ($utenti as $item) :
        
    $sql = "SELECT B.IDlist, B.List FROM " . $wpdb->prefix . "rubriload_usertolist as A, " . $wpdb->prefix . "rubriload_lists AS B WHERE A.IDlist = B.IDlist AND IDuser = " . $item->id;

	$list = $wpdb->get_results($sql);
        
        
        ?>
        	<tr id="<?=$item->id; ?>" <?php echo (++$t%2 == 0) ? " class='alternate' " : ""; ?>>
        	<td class="column-codice">
        		<?php echo $item->id; ?>
        	</td>
        	<td class="column-nome">
        		<?php echo $item->name; ?>
        	</td>
        	<td class="column-email">
        		<?php echo $item->email; ?>
        	</td>
        	<td class="column-liste">
        		<?php 
        		$i = 0;
        		if(is_array($list)) :
        		foreach($list as $l) : 
        		    echo ($i++ > 0) ? ", " : "";
        		    echo $l->List;
        		endforeach;
        		endif;
        		
        		?>
        	</td>
        	</tr>
        <?php endforeach;

endif; 
			
			
		/*	?>

        <tr class="pagination"><td colspan="6">
        	<?php 
        	if($doc[2] > 20){
        		$p = ($doc[1]<$doc[2] - 10) ? $doc[1] + 10 : $doc[2];
				$i = ($doc[1]<10) ? 1 : $doc[1] - 10;
        	}else{
        		$p = $doc[2];
				$i = 1;
        	}
			if($i > 1) :
				?>
				<a href="<?=$urlpage . $flturl;?>&paging=1" >&#171; prima</a>
				<?php
			endif;
        	while($i <= $p) :
				?>
				<a href="<?=$urlpage . $flturl;?>&paging=<?=$i;?>" <?php if($i == $doc[1]):?> class="current" <?php endif; ?>><?=$i;?></a>
				<?php
				$i++;
			endwhile;
			if($p < $doc[2]) :
				?>
				<a href="<?=$urlpage . $flturl;?>&paging=<?=$doc[2];?>" >ultima &#187;</a>
				<?php
			endif;
        	*/?>
        	</td></tr>
        </table>
    </div>
    
    
<?php
    
}


add_filter( 'cron_schedules', 'rlrubrica_add_cron_interval' );
function rlrubrica_add_cron_interval( $schedules ) {
    $schedules['weekly'] = array(
        'interval' => 7*24*60*60,
        'display'  => esc_html__( 'Every Week' ),
    );
 
    return $schedules;
}
if (! wp_next_scheduled ( 'rlrubrica_loading' )) {
wp_schedule_event(time(), 'weekly', 'rlrubrica_loading');
}


add_action('rlrubrica_loading', 'rlrubrica_load');

function rlrubrica_import_page(){
	global $wpdb;
    $url = get_option("rlrubrica_path_xml");
    $args = array();
 $response = wp_remote_get( $url, $args );
 
 $table_name = $wpdb->prefix.'cenini_utenti';
if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
    $cenini = true;
}


    $mail = "";
//var_dump($response);

    if ( is_array( $response ) ) {
      $header = $response['headers']; // array of http header lines
      $body = $response['body']; // use the content
//      echo $body;
      $body     = wp_remote_retrieve_body($response);
      $xml  = simplexml_load_string($body);
      
      //    $wpdb->delete( $wpdb->prefix . "rubriload_usertolist", array() );
    $delete = $wpdb->query("TRUNCATE TABLE " . $wpdb->prefix . "rubriload_usertolist");
    if($cenini){
//    $wpdb->delete( $wpdb->prefix . "cenini_utenti_cat", array() );
    $delete = $wpdb->query("TRUNCATE TABLE " . $wpdb->prefix . "cenini_utenti_cat");
    }
      
     // var_dump($xml);
        foreach($xml->list as $list):
            $name = $list->attributes();


            if($name["name"] == 'Globale'){
            foreach($list as $k){
                
                $dest = $wpdb->get_row( 
                    $wpdb->prepare("
                    SELECT id, name, email FROM " . $wpdb->prefix . "rubriload
                    WHERE id = %d
                        ",
                        array($k->id))
                );

                if(!$dest->id){
                
                    $wpdb->query( 
                        $wpdb->prepare("
                                INSERT INTO " . $wpdb->prefix . "rubriload
                                (id, name, email, flag_controllo)
                                VALUES(%d, %s, %s, 1)
                            ",
                            array($k->id, $k->name, $k->email))
                    );
                    $token = MD5($k->name.date('Y-m-d H:s:i').$k->email);
                    if($cenini){
                        $wpdb->query( 
                        $wpdb->prepare("
                                INSERT INTO " . $wpdb->prefix . "cenini_utenti
                                (nome, mail, stato, token, id_rubrica)
                                VALUES(%s, %s, %d, %s, %d)
                            ",
                            array($k->name, $k->email, 1, $token, $k->id))
                    );
                        
                        
                    }
                    echo "INSERITO: " . $k->id . " " . $k->name . " " . $k->email . "<br />";
                    $mail .= "INSERITO: " . $k->id . " " . $k->name . " " . $k->email . "<br />";
                }else{
                    $upd = 0;
                    $data = array();
                    if($dest->email != $k->email){
                        $upd = 1;
                        $data["email"] = $k->email;
                        $cdata["mail"] = $k->email;
                    }
                    if($dest->name != $k->name){
                        $upd = 1;
                        $data["name"] = $k->name;
                        $cdata["nome"] = $k->name;
                    }
                    $where = array("id"=>$k->id);
                    $cwhere = array("id_rubrica"=>$k->id);
                    if($upd == 1){
                        $wpdb->update(
                            $wpdb->prefix . "rubriload",
                            $data,
                            $where,
                            array('%s', '%s'),
                            array('%d')
                            );
                        
                        
                        if($cenini){
                            $wpdb->update(
                                $wpdb->prefix . "cenini_utenti",
                                $cdata,
                                $cwhere,
                                array('%s', '%s'),
                                array('%d')
                                );
                        }
                        echo "AGGIORNATO: " . $k->id . " - da " . $dest->name . " " . $dest->email . " a " . $k->name . " " . $k->email . "<br />";
                        $mail .=  "AGGIORNATO: " . $k->id . " - da " . $dest->name . " " . $dest->email . " a " . $k->name . " " . $k->email . "<br />";
                    }
                    
//                    echo "aggiorno il flag";
                        $wpdb->update(
                            $wpdb->prefix . "rubriload",
                            array("flag_controllo"=>1),
                            $where,
                            array('%d'),
                            array('%d')
                            );
//                    echo $wpdb->last_query;
                }


            }

           echo "<hr>";
            }else{
                

                
                $lista = $wpdb->get_results( 
                    $wpdb->prepare("
                    SELECT IDlist FROM " . $wpdb->prefix . "rubriload_lists
                    WHERE List = %s
                        ",
                        array($name["name"]))
                );
                $IDlist = $lista[0]->IDlist;
                

                
                
                if(!$IDlist){
                    $wpdb->query( 
                        $wpdb->prepare("
                                INSERT INTO " . $wpdb->prefix . "rubriload_lists
                                (List)
                                VALUES(%s)
                            ",
                            array($name["name"]))
                    );
                   $IDlist =  $wpdb->insert_id;
                   
                   // aggiungo la categoria ai Cenini
                    if($cenini){
                        // verifico se la categoria è già presente
                        $tax = term_exists( $name["name"], 'cenino_categoria' );
                        
                        
                        if(!$tax or !isset($tax["term_id"])){
                            $tax = wp_insert_term(
                              $name["name"], // the term 
                              'cenino_categoria', // the taxonomy
                              array( )
                            );
                        }
                        $IDtax = $tax["term_id"];
            //            var_dump($tax);
            //            echo $IDtax; exit;
                    }
                   
                }else{
                    $tax = term_exists( $name["name"], 'cenino_categoria' );
                        $IDtax = $tax["term_id"];
                }
                foreach($list as $k){
                    $wpdb->query( 
                        $wpdb->prepare("
                                INSERT INTO " . $wpdb->prefix . "rubriload_usertolist
                                (IDlist, IDuser)
                                VALUES(%d, %d)
                            ",
                            array($IDlist, $k->id))
                    );
                    
                    if($cenini){
                        $id_utente = 0;
                        
                        
                        $u = $wpdb->get_results( 
                            $wpdb->prepare("
                            SELECT id_utente, stato FROM " . $wpdb->prefix . "cenini_utenti
                            WHERE id_rubrica = %d
                                ",
                                array($k->id))
                        );
                        $id_utente = $u[0]->id_utente;
                        
                        // riattivo gli utenti doppi
                        if($u[0]->stato == -1){
                            $wpdb->update(
                            $wpdb->prefix . "cenini_utenti",
                            array("stato"=>1),
                            array("id_rubrica"=>$k->id),
                            array('%d'),
                            array('%d')
                            );
                            
                        }
                        
                        
                    $wpdb->query(
                        $wpdb->prepare("
                                INSERT INTO " . $wpdb->prefix . "cenini_utenti_cat
                                (id_categoria, id_utente, categoria)
                                VALUES(%d, %d, %s)
                            ",
                            array($IDtax, $id_utente, $name["name"]))
                    );
                    }
                }
                
                
            }
        endforeach;
        
            // pulisco il db dagli utenti non più presenti
            $user2delete = $wpdb->get_results( 
                    $wpdb->prepare("
                    SELECT id, name, email FROM " . $wpdb->prefix . "rubriload
                    WHERE flag_controllo = %d
                        ",
                        array(0))
                );

            foreach($user2delete as $item){

                if($cenini){
                     $wpdb->update(
                            $wpdb->prefix . "cenini_utenti",
                            array("stato"=>-1),
                            array("id_rubrica"=>$item->id),
                            array('%d'),
                            array('%d')
                            );
                }
                
                $wpdb->delete(
                            $wpdb->prefix . "rubriload",
                            array("id"=>$item->id),
                            array('%d')
                    
                    );
                    
                echo "ELIMINATO: " . $item->id . " " . $item->name . " " . $item->email . "<br />";
                $mail .=  "ELIMINATO: " . $item->id . " " . $item->name . " " . $item->email . "<br />";
            }
            
            
            // pulisco il db dalle mail doppie (solo in cenini_utenti)
            // prima gestisco la categoria Anatroccoli
            $user2disabled = $wpdb->get_results( 
                    "
                    SELECT mail FROM " . $wpdb->prefix . "cenini_utenti
                    WHERE stato = 1
                    GROUP BY mail
                    having count(mail) > 1
                        "
                );
//            var_dump($user2disabled);
//            echo "<hr />";
            foreach($user2disabled as $item){
                $user = $wpdb->get_results( 
                     
                    $wpdb->prepare("
                    SELECT U.id_utente, U.nome, U.cognome, U.mail, C.id_categoria, C.categoria FROM " . $wpdb->prefix . "cenini_utenti as U, " . $wpdb->prefix . "cenini_utenti_cat as C
                    WHERE U.mail = %s
                      AND U.id_utente = C.id_utente
                      AND U.stato = 1
                      
                    UNION

                    SELECT U.id_utente, U.nome, U.cognome, U.mail, '', '' FROM " . $wpdb->prefix . "cenini_utenti as U WHERE U.mail = %s AND U.stato = 1 AND U.id_utente NOT IN (SELECT id_utente FROM " . $wpdb->prefix . "cenini_utenti_cat) 

                    ORDER BY id_utente
                        ", array($item->mail, $item->mail))
                );
                
    //            echo $wpdb->last_query;
                
                $n_user = $wpdb->num_rows;
                
                $arr = array();
                $anatroccoli = array();
       
                // formatto l'array
                foreach($user as $u){
                    $arr[$u->id_utente][$u->id_categoria] = $u->categoria;
                    
                }
                // verifico che l'array contenga solo Anatroccoli
                foreach($arr as $k=>$v){
                    if(in_array("Anatroccoli", $v )){
                        $anatroccoli[] = $k;
                        unset($arr[$k]["17"]);
                        unset($v["17"]);
                    }else{
                        $user_id = $k;
                    }
                    if(count($v)==0){
                        unset($arr[$k]);
                    }
                }
                
                // verifico che ci sia almeno un "adulto"

                if(count($anatroccoli) < $n_user) :
                
                    $wpdb->query(
                        $wpdb->prepare("
                                INSERT INTO " . $wpdb->prefix . "cenini_utenti_cat
                                (id_categoria, id_utente, categoria)
                                VALUES(%d, %d, %s)
                            ",
                            array("17", $user_id, "Anatroccoli"))
                    );
                    
                    foreach($anatroccoli as $a){
                        
                        
                     $wpdb->update(
                            $wpdb->prefix . "cenini_utenti",
                            array("stato"=>-1),
                            array("id_utente"=>$a),
                            array('%d'),
                            array('%d')
                            );
                        
                    }
                
                else : // sono tutti anatroccoli
                
                    foreach($anatroccoli as $a){
                        continue;
                        
                     $wpdb->update(
                            $wpdb->prefix . "cenini_utenti",
                            array("stato"=>-1),
                            array("id_utente"=>$a),
                            array('%d'),
                            array('%d')
                            );
                        
                    }
                endif;
        
            }
            
            // poi tutte le altre categorie
            $user2disabled2 = $wpdb->get_results( 
                    "
                    SELECT mail FROM " . $wpdb->prefix . "cenini_utenti
                    WHERE stato = 1
                    GROUP BY mail
                    having count(mail) > 1
                        "
                );
            foreach($user2disabled2 as $item){
                
                $user = $wpdb->get_results( 
                     
                    $wpdb->prepare("
                    SELECT U.id_utente, U.nome, U.cognome, U.mail, C.id_categoria, C.categoria FROM " . $wpdb->prefix . "cenini_utenti as U, " . $wpdb->prefix . "cenini_utenti_cat as C
                    WHERE U.mail = %s
                      AND U.id_utente = C.id_utente
                      AND U.stato = 1
                      
                    UNION

                    SELECT U.id_utente, U.nome, U.cognome, U.mail, '', '' FROM " . $wpdb->prefix . "cenini_utenti as U WHERE U.mail = %s AND U.stato = 1 AND U.id_utente NOT IN (SELECT id_utente FROM " . $wpdb->prefix . "cenini_utenti_cat) 

                    ORDER BY id_utente
                        ", array($item->mail,$item->mail))
                );
                $arr = array();
                foreach($user as $u){
                    $arr[$u->id_utente][$u->id_categoria] = $u->categoria;
                    
                }
                
                if(count($arr) > 1){
                    $i = 0;
                    $id = 0;
                    $id_old = 0; // l'utente da disabilitare
                    
                    foreach($arr as $k=>$v){
                        if($i == 0){
                            
                            $i = count($v);
                            $id = $k;
                        }else{
                            if($i <= count($v)){
                                $id_old = $id;
                                $id = $k;
                                // annulliamo l'id_old
                                $wpdb->update(
                                    $wpdb->prefix . "cenini_utenti",
                                    array("stato"=>-1),
                                    array("id_utente"=>$id_old),
                                    array('%d'),
                                    array('%d')
                                );
                                // copiamo tutte le categorie del vecchio id nel nuovo id
                                foreach($arr[$id_old] as $cat_id=>$cat){
                                    
                                    
                                    if(!in_array($cat_id, $arr[$id])){
                                        $wpdb->query(
                                            $wpdb->prepare("
                                                    INSERT INTO " . $wpdb->prefix . "cenini_utenti_cat
                                                    (id_categoria, id_utente, categoria)
                                                    VALUES(%d, %d, %s)
                                                ",
                                                array($cat_id, $id, $cat))
                                        );
                                        $arr[$id][$cat_id] = $cat;
                                    }
                                }
                                
                                
                            }else{
                                // $id contiene l'id buono
                                // $k contiene l'id da disabilitare
                                $id_old = $k;
                                // annulliamo l'id_old
                                $wpdb->update(
                                    $wpdb->prefix . "cenini_utenti",
                                    array("stato"=>-1),
                                    array("id_utente"=>$id_old),
                                    array('%d'),
                                    array('%d')
                                );
                                // copiamo tutte le categorie del vecchio id nel nuovo id
                                foreach($arr[$id_old] as $cat_id=>$cat){
                                    
                                    
                                    if(!in_array($cat_id, $arr[$id])){
                                        $wpdb->query(
                                            $wpdb->prepare("
                                                    INSERT INTO " . $wpdb->prefix . "cenini_utenti_cat
                                                    (id_categoria, id_utente, categoria)
                                                    VALUES(%d, %d, %s)
                                                ",
                                                array($cat_id, $id, $cat))
                                        );
                                        $arr[$id][$cat_id] = $cat;
                                    }
                                }
                            }
                        }
                        
                    }
                    
                }
            }
            
            /*
            $wpdb->query( 
                $wpdb->prepare( 
                    "UPDATE " . $wpdb->prefix . "rubriload
                     SET `flag_controllo` = %d",
                     0
                )
            );*/


	$testomail = get_option("rlrubrica_template_report");
	$testomail = str_replace('%dati%', $mail, $testomail);
        
        
	$x = wp_mail(get_option("rlrubrica_loading_report__to"), get_option("rlrubrica_loading_report__subject"), $testomail, array("From: " . get_option('rlrubrica_loading_report__from') . " <" . get_option('rlrubrica_loading_report__from') . ">", 'Content-Type: text/html; charset=UTF-8'));
    }





    rlrubrica_alignNewsletter();
}




function rlrubrica_getGuid() { 
    $characters = 'abcdefghijklmnopqrstuvwxyz'; 
    $randomString = ''; 
    $n = 6;
    $m = 5;
    for ($j = 0; $j < $m; $j++){
        $randomString .= ($randomString != '') ? '-' : '';
        for ($i = 0; $i < $n; $i++) { 
            $index = rand(0, strlen($characters) - 1); 
            $randomString .= $characters[$index]; 
        }
    }
  
    return $randomString; 
} 
  



// allinea la rubrica di Email Subscribers
function rlrubrica_alignNewsletter(){
    global $wpdb;
    
    $mail = $wpdb->get_results("
    select * from " . $wpdb->prefix . "rubriload
        where email not in (select es_email_mail from " . $wpdb->prefix . "es_emaillist)
    ");
    
    foreach($mail as $u){
        
        $cat = $wpdb->get_results(
            $wpdb->prepare("
            SELECT B.List from " . $wpdb->prefix . "rubriload_usertolist as A, " . $wpdb->prefix . "rubriload_lists as B
            WHERE A.IDuser = %d and A.IDlist = B.IDlist and B.List IN ('Trieste', 'Anatroccoli', 'Polisportiva', 'Donatori')
            ", $u->ID)
            );


        // inserisce Contrada a tutti
        $wpdb->insert( 
        	$wpdb->prefix . "es_emaillist", 
        	array( 
        		'es_email_name' => $u->name, 
        		'es_email_mail' => $u->email,
        		'es_email_status' => 'Confirmed',
        		'es_email_created' => date('Y-m-d H:i:s'),
        		'es_email_group' => "Contrada",
        		'es_email_guid' => rlrubrica_getGuid()
        	), 
        	array( 
        		'%s', 
        		'%s',
        		'%s',
        		'%s',
        		'%s',
        		'%s'
        	) 
        );

        foreach($cat as $lista){
            
            $wpdb->insert( 
            	$wpdb->prefix . "es_emaillist", 
            	array( 
            		'es_email_name' => $u->name, 
            		'es_email_mail' => $u->email,
            		'es_email_status' => 'Confirmed',
            		'es_email_created' => date('Y-m-d H:i:s'),
            		'es_email_group' => $lista->List,
            		'es_email_guid' => rlrubrica_getGuid()
            	), 
            	array( 
            		'%s', 
            		'%s',
            		'%s',
            		'%s',
            		'%s',
            		'%s'
            	) 
            );

        }
        
    }
    
    

}