<?php
#$define('IMCONVERT','/usr/local/bin/convert');

/*
img_resize('8march.jpg',400,300,'test_resize1.jpg',array('border'=>1));
img_resize('8march.jpg',400,300,'test_resize2.jpg',array('border'=>0));
img_resize('test_h.jpg',400,300,'test_resize3.jpg',array('border'=>1));
img_resize('test_h.jpg',400,300,'test_resize4.jpg',array('border'=>0));
img_resize('8march.jpg',300,400,'test_resize5.jpg',array('border'=>1));
img_resize('8march.jpg',300,400,'test_resize6.jpg',array('border'=>0));
img_resize('test_h.jpg',300,400,'test_resize7.jpg',array('border'=>1));
img_resize('test_h.jpg',300,400,'test_resize8.jpg',array('border'=>0));
img_resize('test_h.jpg',300,400,'test_resize9.jpg',array('crop'=>1));
img_resize('test_h.jpg',400,300,'test_resize10.jpg',array('crop'=>1));
img_resize('8march.jpg',300,400,'test_resize11.jpg',array('crop'=>1));
img_resize('8march.jpg',400,300,'test_resize12.jpg',array('crop'=>1));
*/

//ob_start();

function img_resize($fname,$w,$h,$target,$params) {
    $img = getimagesize($fname);
	$bordercolor = isset($params['bordercolor'])?$params['bordercolor']:'white';

#если nomagnifying==1 и картинка меньше чем нужно - то не увеличиваем
    if (@ $params['nomagnifying'] == 1 && $img[0]<$w && $img[1]<$h ) {
        $w = $img[0];
        $h = $img[1];
    }

    $w_delta = $img[0]/$w;
    $height = round($img[1]/$w_delta);
    $h_delta = $img[1]/$h;
    $width = round($img[0]/$h_delta);

	switch (true) {
        case (int)@$params['scale_up']==1:
            if ( $img[0]<$w && $img[1]<$h ) {
                $command = '';
                break;
            }
		case (int)@$params['border']==1:
			$ext_command = ' -bordercolor '.$bordercolor.' -border %sx%s';
		case (int)@$params['crop']==0:
			if ( $height>=$h ) {
				$command = ' -resize x'.$h;
				$borderheight = 0;
				$borderwidth = 0;
				if ( $height!=$h )
				    $borderwidth = round(($w-$width)/2);
			} else {
                $command = ' -resize '.$w.'x ';
                $borderwidth = 0;
                $borderheight = 0;
				if ( $width!=$w )
                    $borderheight = round(($h-$height)/2);
			}
			
			if ( @$params['border']==1 && $height!=$h ) {
				$ext_command = sprintf($ext_command,$borderwidth,$borderheight);
				$command = $command.$ext_command;
                
                $w_over = $h_over = 0;
                if ( $borderwidth>0 )
				    $w_over = $w - ($width+$borderwidth*2);
				if ( $borderheight>0 )
				    $h_over = $h - ($height+$borderheight*2);
				    
				if ( $w_over<0 || $h_over<0 ) {
					$command .= ' -crop -'.abs($w_over).'-'.abs($h_over);
				}
			}
		break;
		case (int)@$params['crop']==1:
            if ( $height<=$h && $width>=$w ) {
                $tw = ceil(($width-$w)/2);
                $command = ' -resize x'.$h.' -crop '.$w.'x'.$h.'+'.$tw.'+0';
            } else {
                $th = ceil(($height-$h)/2);
                $command = ' -resize '.$w.'x -crop '.$w.'x'.$h.'+0+'.$th;
            }
			        break;
		default:
			;
		break;
	}
#    echo '-------'.IMCONVERT.$command.' '.$fname.' '.$target."\n";	
#    @file_put_contents('tmp/resize.log',IMCONVERT.$command.' '.$fname.' '.$target."\n".print_r($params,true));
	system(IMCONVERT.$command.' '.$fname.' '.$target);
}

//$ss = ob_get_contents();
//file_put_contents('tmp/resize.log',$ss);
//ob_end_clean();


#img_resize('welcome1-2.png',400,300,'test_resize2.jpg',array('crop'=>1));

