<?php get_header();    
  global $options;  



   
  if ( $options['slider-aktiv'] == "1" ){ ?>  
    <div class="section teaser">
        <div class="row">
            <?php get_sidebar( 'teaser' ); ?>
        </div>  
    </div>
<?php } ?>
<div class="section content" id="main-content">
  <div class="row">
    <div class="content-primary">
      <div class="skin">

          <?php if ( is_active_sidebar( 'startpage-intro-area' ) ) { 
                 dynamic_sidebar( 'startpage-intro-area' );
           } ?>          
          
          
      <?php
      $foundarticles=0;
      $i = 0; 
      $col = 0; 
      $cols = array();
     
      global $wp_query;
      if ($options['artikelstream-type']==1) {
           /* 1: Alle Artikel, ohne Linktipps */
           $args =  $wp_query->query;
      } elseif ($options['artikelstream-type']==2) {
          /* 2: Alle Artikel aus Kategorien bis auf definierte Cats und ohne Linktipps */
          if (isset($options['artikelstream-exclusive-catliste']) 
                  && (is_array($options['artikelstream-exclusive-catliste']))) {  
              $catliste = '';
              $poscatliste  = '';
             
              foreach ($options['artikelstream-exclusive-catliste'] as $cat) {
                  if (strlen($catliste)>1) {
                      $catliste .= ",";
                      $poscatliste .= ",";
                  }
                  $catliste.= '-'.$cat;
                  $poscatliste .= $cat;
              }
              $args = 'cat='.$catliste;      
          } else {
              $args = $wp_query->query;
          }
      } else {
        if ($options['aktiv-linktipps']==1) {	    
	    $args = array_merge( $wp_query->query, array( 'post_type' => array('linktipps','post') ) );	    
        } else {
            $args =  $wp_query->query;
        }
      }
      $numentries = $options['artikelstream-maxnum-main'] + $options['artikelstream-nextnum-main'];
      if (is_array($args)) {
        $args = array_merge( $args, array( 'posts_per_page' => $numentries ) );
      } else {
          $args .= '&posts_per_page='.$numentries;
      }  
     
      query_posts( $args ); 
      
      $continuelinks = '';
      while (have_posts() && $i<$numentries) : the_post();
	  $i++;
          $output = '';
	  if (($options['artikelstream-nextnum-main']>0) && ($i>$options['artikelstream-maxnum-main'])) {	      
	      $continuelinks .= '<li><a href="'.get_permalink().'">'.get_the_title().'</a></li>';
	      $continuelinks .= "\n";
	  } else {
	    if (( isset($options['artikelstream-numfullwidth-main']))
		      && ($options['artikelstream-numfullwidth-main']>=$i )) {
		  $output = piratenkleider_post_teaser($options['teaser-titleup'],$options['teaser-datebox'],$options['teaser-dateline'],$options['teaser_maxlength'],$options['teaser-thumbnail_fallback'],$options['teaser-floating']);
	    } else {
		  $output =piratenkleider_post_teaser($options['teaser-titleup-halfwidth'],$options['teaser-datebox-halfwidth'],$options['teaser-dateline-halfwidth'],$options['teaser-maxlength-halfwidth'],$options['teaser-thumbnail_fallback'],$options['teaser-floating-halfwidth']);
	    }
	    if (isset($output)) {
	      $cols[$col++] = $output;
	    }
	  }
      endwhile;
      // Reset Query
       wp_reset_query();
       if (isset($continuelinks) && strlen($continuelinks)>1) {
	   $linkliste = "<h2>".$options['artikelstream-title-maincontinuelist']."</h2>\n";
	   $linkliste .= "<ul>\n".$continuelinks."</ul>\n";
	   $cols[$col++] = $linkliste;
       }       

        if (($options['artikelstream-type']==1) || ($options['artikelstream-type']==2)) {
	   echo '<div id="main-stream">';

	   if (isset($options['artikelstream-title-main']) && (strlen($options['artikelstream-title-main'])>0)) {
		echo '<h1>'.$options['artikelstream-title-main'].'</h1>';       
		echo "\n";
	   }
	}
        echo '<div class="columns">';
        $z=1;
        foreach($cols as $key => $col) {
            if (( isset($options['artikelstream-numfullwidth-main']))
                && ($options['artikelstream-numfullwidth-main']>$key )) {
                    echo $col;                                               
                } else {          
                     if (( isset($options['artikelstream-numfullwidth-main']))
                            && ($options['artikelstream-numfullwidth-main']==$key )
                             && ($options['artikelstream-numfullwidth-main']>0 )) {
                         echo '<hr>';
                        }                                              
                    echo '<div class="column'.$z.'">' . $col . '</div>';                            
                    $z++;
                    if ($z>2) {
                        $z=1;
                        echo '<hr class="clear">';
                    }
                }     
                $foundarticles =1;
        }

        if ($z==2) {
            echo '<hr class="clear">';
        }
        echo "</div>\n";
        echo "</div>\n";
       
        

        if ($options['artikelstream-type']>0) {
              /* Zuerst Linktipps */
             if  ($options['artikelstream-show-linktipps']==1) { 
		 query_posts(  array( 'post_type' => array('linktipps') ) ); 
		 global $post;
		 $linktippout = '';
		 $i=0;
		 $continuelinks = '';
		 $numentries = $options['artikelstream-maxnum-linktipps']+ $options['artikelstream-nextnum-linktipps'];
		 $z=1;
		 
		 $linktippout .= '<div class="columns">';
		 while (have_posts() && $i<$numentries) : the_post();
		     $i++;    
		     if ($i<=$options['artikelstream-maxnum-linktipps']) {
			$out = linktipp_display($post);
			
			$linktippout .= '<div class="column'.$z.'">' . $out . '</div>';                            
			$z++;
			if ($z>2) {
			    $z=1;
			    $linktippout .=  '<hr class="clear">';
			}	
		     } elseif ($options['artikelstream-nextnum-linktipps']>0) {
			 $link = esc_attr( get_post_meta( $post->ID, 'linktipp_url', true ) ); 		 
			 $continuelinks .= '<li><a class="extern" href="'.$link.'">'.get_the_title().'</a></li>';
			 $continuelinks .= "\n";
		     }
		 endwhile;
		 
		 if (isset($continuelinks) && strlen($continuelinks)>0) {		     
		    $linkliste = '<div class="column'.$z.'">';
                    if (isset($options['artikelstream-title-linktippcontinuelist']) && (strlen($options['artikelstream-title-linktippcontinuelist'])>1)) {
                        $linkliste .= "<h2>".$options['artikelstream-title-linktippcontinuelist']."</h2>\n";
                    }
		    $linkliste .= "<ul class=\"extern\">\n".$continuelinks."</ul>\n";
		    $linkliste .= "</div>\n";
		    $z++;
			if ($z>2) {
			    $z=1;
			    $linkliste .=  '<hr class="clear">';
			}
			$linktippout .= $linkliste;
		 }  
		 
		 if ($z==2) {
			$linktippout .= '<hr class="clear">';
		  }		
		  $linktippout .= "</div>\n";
		 
		 wp_reset_query();
		 if (isset($linktippout) && strlen($linktippout)>1) {
		     echo '<div id="linktipp-stream">';
                     if (isset($options['artikelstream-title-linktipps']) && (strlen($options['artikelstream-title-linktipps'])>1)) {
                         echo '<h1>'.$options['artikelstream-title-linktipps'].'</h1>';
                     }
		     echo "\n";
		     echo $linktippout;
    
		    echo "</div>\n";
		     $foundarticles =1;
		 }
	     }

             if (($options['artikelstream-type']==2) && ($options['artikelstream-show-second']==1)) {
                 /* Ausnahme-Cats */
                 
                  query_posts( 'cat='.$poscatliste ); 
                    $numentries = $options['artikelstream-maxnum-second'] + $options['artikelstream-nextnum-second'];
                    $i=0;
		    $cols = array();
		    $col=0;
		    $continuelinks = '';
                  while (have_posts() && $i<$numentries) : the_post();
                      $i++;			      
		       if (($options['artikelstream-nextnum-second']>0) && ($i>$options['artikelstream-maxnum-second'])) {	      
			    $continuelinks .= '<li><a href="'.get_permalink().'">'.get_the_title().'</a></li>';
			    $continuelinks .= "\n";
			} else {
			   if (( isset($options['artikelstream-numfullwidth-second']))
				 && ($options['artikelstream-numfullwidth-second']>=$i )) {
				$output = piratenkleider_post_teaser($options['teaser-titleup'],$options['teaser-datebox'],$options['teaser-dateline'],$options['teaser_maxlength'],$options['teaser-thumbnail_fallback'],$options['teaser-floating']);
			    } else {
				$output =piratenkleider_post_teaser($options['teaser-titleup-halfwidth'],$options['teaser-datebox-halfwidth'],$options['teaser-dateline-halfwidth'],$options['teaser-maxlength-halfwidth'],$options['teaser-thumbnail_fallback'],$options['teaser-floating-halfwidth']);
			    }

			   if (isset($output)) {
				$cols[$col++] = $output;
			    }
			}
                  endwhile;
		  
		  if (isset($continuelinks) && strlen($continuelinks)>1) {
		    $linkliste = "<h2>".$options['artikelstream-title-secondcontinuelist']."</h2>\n";
		    $linkliste .= "<ul>\n".$continuelinks."</ul>\n";
			$cols[$col++] = $linkliste;
		    }    
		  
                    if ($col>0) {
                        echo '<div id="second-stream">';
                        if (isset($options['artikelstream-title-second']) && (strlen($options['artikelstream-title-second'])>1)) {
                            echo '<h1>'.$options['artikelstream-title-second'].'</h1>';
                        }
			echo "\n";	
                        echo '<div class="columns">';
                        $z=1;
                        foreach($cols as $key => $col) {
                            if (( isset($options['artikelstream-numfullwidth-second']))
                                && ($options['artikelstream-numfullwidth-second']>$key )) {
                                    echo $col;                                               
                                } else {          
                                     if (( isset($options['artikelstream-numfullwidth-second']))
                                            && ($options['artikelstream-numfullwidth-second']==$key )
                                             && ($options['artikelstream-numfullwidth-second']>0 )) {
                                         echo '<hr>';
                                        }                                              
                                    echo '<div class="column'.$z.'">' . $col . '</div>';                            
                                    $z++;
                                    if ($z>2) {
                                        $z=1;
                                        echo '<hr class="clear">';
                                    }
                                }     
                                $foundarticles =1;
			    }
			    if ($z==2) {
				echo '<hr class="clear">';
			    }		
			    echo "</div>\n";			
			
                        echo "</div>\n";
			$foundarticles =1;
                    }
             }
            echo "</div>\n";
        }
        
	if ($foundarticles==0) { ?>
            <h2><?php _e("Nichts gefunden", 'piratenkleider'); ?></h2>
            <p>
            <?php _e("Es konnten keine Artikel gefunden werden. Bitte versuchen Sie es nochmal mit einer Suche.", 'piratenkleider'); ?>
            </p>
            <?php get_search_form(); 
            echo "<hr>\n"; 

	}
        get_sidebar( 'startpage-contentfooter' ); ?>

      <
    </div>
    <div class="content-aside">
      <div class="skin">
          <h1 class="skip"><?php _e( 'Weitere Informationen', 'piratenkleider' ); ?></h1>
            <?php get_sidebar(); ?>
      </div>
    </div>
  </div>
  <?php  get_piratenkleider_socialmediaicons(2); ?>

</div>

<?php get_footer(); ?>