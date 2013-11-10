<?php

function LienEditer(){
		global $post, $user_ID;
		if(!get_current_user_id()){
			echo home_url().'/wp-admin';
			return;
		}
		if(is_singular()){
			echo get_edit_post_link($post->ID);;
		}else{
			$type = get_queried_object();			
			if(!$type){
				echo home_url().'/wp-admin';
			}else{
				echo home_url().'/wp-admin/edit.php?post_type='.$type->name;
			}
		}
	}

?>
<div class="navbar" style="margin-bottom: 0;">
  <div class="navbar-inner" style="background: none; padding-left: 5px;"> 
    <ul class="nav">
    
      <li>
      
		<a target="int" href="<?php LienEditer(); ?>">Editer</a>      	
      
      </li>
    
    
    </ul>
  </div>
</div>

      <!--li class="active"><a class="bouton-nav" href="#">Home</a></li>
      <li><a class="bouton-nav" href="#">Link</a></li>
      <li><a class="bouton-nav" href="#">Link</a></li-->
