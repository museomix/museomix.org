<?php
	function TitrePage(){
		
		if(is_front_page()){
			echo 'Museomix';
		}
		
		elseif(is_home() || is_category() ){
			echo '<a class="ln-titre-page" href="'.get_permalink(get_option('page_for_posts')).'">Actualités</a>';
		}
		
		elseif( is_date() ){
			echo '<a class="ln-titre-page" href="'.get_permalink(get_option('page_for_posts')).'">Actualités</a>';
		}
		
		elseif(is_singular('post')){
			echo '<a class="ln-titre-page" href="'.get_permalink(get_option('page_for_posts')).'">Actualités</a>';
		}
		
		elseif(is_singular()){
			the_title();
		}
		
		elseif(is_archive()){
			$archive = get_queried_object();
			echo $archive->labels->name;
		}
	}

	function AccrochePage(){
		global $post;
		
		if(is_archive() && !is_category() && !is_date()){
			return;
		}
		
		if(is_home()){
			$id = get_option('page_for_posts');
			$accroche = get_field('sous-titre',$id);
		}
		
		elseif(is_category()){
			$accroche = '@'.get_queried_object()->name;
		}
		
		elseif(is_date()){
			if(is_month())
				$accroche = get_the_date('F Y');
			elseif(is_year())
				$accroche = get_the_date('Y');
			else
				$accroche = get_the_date();
		}
		
		elseif(is_singular('post')){
			$cat = get_the_category($post->ID);
			$accroche = '<a class="ln-titre-page" href="'.get_category_link($cat[0]->term_id).'">@'.$cat[0]->name.'</a>';
		}
		
		else{
			$accroche = get_field('sous-titre',$post->ID);	
		}
		
		if($accroche){
			echo $accroche; 
		}
		elseif('sponsor'==$post->post_type){
			echo 'Partenaire de Musemomix';
		}
		else{
			bloginfo('description');
		}		
		return;
	}
	
	function TitreContenu(){
		if(!is_singular('post')) return;
		echo get_the_title();
	}

	function VisuelPage() {
		if(is_singular('post') || is_category()) {

			$cat = get_the_category($post->ID);
			$lieu_id = substr(category_description($cat[0]->term_id),3,2);
			echo 'lieu='.$lieu_id;
			if(!empty($lieu_id)) {
				$id = get_field('visuel_page',$lieu_id);
				$img = wp_get_attachment_image_src($id,"location_thumbnail");
			} else {
				$img = wp_get_attachment_image_src(203,"location_thumbnail");
			}
			echo $img[0];

		} else if(get_field('visuel_page')) {
			$id = get_field('visuel_page');
			$img = wp_get_attachment_image_src($id,"location_thumbnail");
			echo $img[0];
		} else {
			$id = get_field('visuel_page',get_field('museomix',$id)->ID);
			if(empty($id)) $id = 203;
			$img = wp_get_attachment_image_src($id,"location_thumbnail");
			echo $img[0];
		}

	}
		
?>
<?php if( ! is_front_page() ) : ?> 
	<header class="jumbotron subhead "  style="border-bottom: 1px solid #ccc; position: relative; min-height: 241px; background:#ffffff url('<?php VisuelPage(); ?>') no-repeat left top; ">
	
	
	  <div class="container" style="padding: 70px 0 0 0;  border: 1px none #ccc; position: relative; z-index: 3;
	  ">
		<h1 style="font-size: 60px; letter-spacing: 1px; margin: 0 40px 0 35px;"><?php TitrePage(); ?></h1>

		<h2 class="lead" style="margin: 40px 40px 0 35px; "><?php AccrochePage(); ?>
			
		<?php
			$socialMedia = DisplaySocialMedia();
			if (trim($socialMedia)!='') { ?>
				<br />
				<small>Follow us ! <?php echo $socialMedia; ?></small>
			<?php } ?>
		</h2>

		<?php if(is_singular('post')): while(have_posts()): the_post();   ?>
		
			
				

		<?php endwhile; rewind_posts(); endif; ?>

	  </div>

	<div style="clear: both;"></div>
	</header>
	<?php else: ?>
	
	<?php endif; ?>