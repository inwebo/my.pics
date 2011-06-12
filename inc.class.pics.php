<?php
/**
* Fichier � inclure inc.class.pics.php5
*
* LICENSE: Paternit� - Pas d'Utilisation Commerciale
*
* @category inc
* @package inc.class
* @subpackage inc.class.pics
* @copyright 2011 Inwebo
* @license http://creativecommons.org/licenses/by-nc-nd/2.0/fr/
* @version Mai 2011
* @link http://www.inwebo.net/
* @since Novembre 2009
*/ 


/**
 * Cr�ation et manipulation d'images
 *
 * Cr�ation et manipulation d'images gr�ce � la librairie GD2 et PHP5, attention pensez � activer GD2 !
 * Permet la manipulation d'image (rogner, filtres etc) et leur sauvegarde sans alt�rer le fichier source.
 *
 *
 * MIXED	__construct()	: 1 si instanciation r�ussie sinon lance exception
 * MIXED	newPics()		: Ressource si cr�ation de l'image r�ussie sinon lance exception
 * MIXED	open()			: Ressource si l'image est ouverte correctement sinon lance exception
 * STRING	name			: Retourne le nom d'un fichier sans son extension
 * ARRAY	info			: Retourne la largeur et la hauteur d'une image
 * MIXED	merge()			: 0 si les images n'existent pas, sinon renvoi la resource image de deux images fusionn�es
 * MIXED	resize()		: 0 si les images n'existent pas, sinon renvoi la resource image redimensionn�e
 * MIXED	crop()			: 0 si les images n'existent pas, sinon renvoi la resource image de deux images rogn�e
 * RESOURCE filter()		: RESOURCE image avec un filtre appliqu�
 * RESOURCE mask()			: RESOURCE image avec un masque d'opacit� appliqu� sinon 0
 * RESOURCE pattern()		: 1 si motif appliqu� sur une image sinon exception
 * RESOURCE	display()		: Affiche une ressource image dans le navigateur
 * BOOL		save()			: 1 si la sauvegarde s est correctement d�roul�, sauvegarde la ressource courante sous forme d'un fichier image
 * BOOL		saveByActions()	: Sauvegarde les traitements dans un fichier
 * BOOL		LoadByActions()	: Charge des traitements
 * VOID		reset			: Vide la pile courante de traitement �ffectu�s sur la ressource image
 * STRING	__tostring		: Infos sur l'objet courant
 */ 

/**
 * D�finition des flags n�cessaires.
 */
 	// Type
	define ('PNG', 'PNG');
	define ('JPEG', 'JPEG');
	define ('GIF', 'GIF');
	define ('JPG', 'JPEG');

	// Palette
	define ('TRUE_COLOR', 'TRUE_COLOR');
	define ('INDEXED', 'INDEXED');

	// New filter
	define ('IMG_FILTER_SEPIA', 'IMG_FILTER_SEPIA');
	
	// Position CROP
	define ('TOP_LEFT','TOP_LEFT');
	define ('TOP', 'TOP');
	define ('TOP_RIGHT','TOP_RIGHT');
	define ('RIGHT', 'RIGHT');
	define ('BOTTOM_RIGHT', 'BOTTOM_RIGHT');
	define ('BOTTOM', 'BOTTOM');
	define ('BOTTOM_LEFT', 'BOTTOM_LEFT');
	define ('LEFT', 'LEFT');
	define ('CENTER', 'CENTER');
	define ('CUSTOM', 'CUSTOM');
	
	// Alpha
	define ('RESIZE_TO_MASK_SIZE', 'RESIZE_TO_MASK_SIZE');
	define ('RESIZE_TO_PICTURES_SIZE', 'RESIZE_TO_PICTURES_SIZE');
	
	// Save
	define ( 'NEW_FILE', 'NEW_FILE' );
	define ( 'REPLACE_FILE', 'REPLACE_FILE' );
	define ( 'STACK', 'STACK' );
	
	// Type instance
	define ( 'LOAD_FILE', 'LOAD_FILE');
	define ( 'NEW_GD', 'NEW_GD');

class Pics {
	
	// Ressource GD courante
	public $_gdPics;
	public $height;
	public $width;
	public $picsType;
	public $src;
	// Pile de traitement faits sur l'image courante
	public $stack;
	
	// getters
	public function getHeight() {
		return $this->height;
	}
		
	public function getWidth() {
		return $this->width;
	}
	
	public function getPicsType() {
		return $this->type;
	}
	
	public function getGdPics() {
		return $this->_gdPics;
	}
	
    /**
     * Instanciation de la class ouverture d'une image existante $nameOrLength
	 * Ou cr�ation d'une nouvelle ressource image de longueur $nameOrLength
	 * et de hauteur $height.
	 *
	 * @arguments	$nameOrLength	STRING	Chemin d'acc�s d'une image
	 *								INT		Largeur d'une nouvelle image
	 *
	 * @arguments	$height			NULL	Si $nameOrLength est une chaine
	 *								INT		Hauteur d'une nouvelle image
	 *
	 *
	 * @return    	TRUE		Si la nouvelle ressource image est cr�e avec succ�s
	 *
	 * @throw		EXCEPTION	Si $nameOrLength n'est pas un chemin d'acc�s valide
	 *							Ou si $nameOrLength et $height ne sont pas des entiers
     */
	public function __construct( $nameOrLength, $height = NULL ) {
		
		$this->stack = array();
	
		if ( !extension_loaded( 'gd' ) ) {
			throw new Exception('GD2 disabled');
		}
	  
		if( is_file( $nameOrLength ) && $height === NULL ) {	  
			$this->_gdPics 	= $this->open( $nameOrLength ) ;
			$this->src 		= $nameOrLength ;
			$this->type 	= LOAD_FILE ;
			$this->height	= imagesy($this->_gdPics) ;
			$this->width	= imagesx($this->_gdPics) ;
		}
		elseif( is_int( $nameOrLength ) && is_int( $height ) ) {
			// Demande une nouvelle ressource image
			$this->_gdPics	= $this->newPics( $nameOrLength, $height ) ;
			$this->width	= $nameOrLength ;
			$this->height	= $height ;
			$this->type 	= NEW_GD ;
		}
		else {
			throw new Exception('Missing ressource or pics doesn\'t exist');
		}
		$this->stack[] = array( __FUNCTION__, func_get_args() );
		return TRUE;
	  
	}
		
	/**
     * Cr�er une nouvelle ressource image, de largeur $largeur, de hauteur $hauteur, avec comme nombre de couleur
	 * $flag, de couleur RGB $R, $G, $B, et comme opacit� $alpha
	 *
	 *
	 * @arguments 	INT $largeur, largeur en pixel
	 *				INT $hauteur, hauteur en pixel
	 *				STRING $flag DEFAULT TRUE_COLOR image 32 bits
	 * 									 INDEXED	image 1 bits
	 *				INT $alpha opacite 0 : opacite complete, 127 transparence complete
	 * @return    	RESOURCE images
     */
	public function newPics( $largeur, $hauteur, $flag = TRUE_COLOR, $R = '255', $G = '255', $B = '255', $alpha = '127' ) {
		switch ($flag) {
			case TRUE_COLOR :
					$pictures = imagecreatetruecolor($largeur, $hauteur);
					$fond 	  = imagecolorallocatealpha($pictures,  $R, $G, $B, $alpha);
					imagefill($pictures, 0, 0, $fond);
					imagesavealpha($pictures, true);
				break;
				
			case INDEXED :
					$pictures = imagecreate($largeur, $hauteur);
					$fond 	  = imagecolorallocatea($pictures,  $R, $G, $B);
					imagefill($pictures, 0, 0, $fond);
				break;
				
			default :
					throw new Exception ('Bad $flag, try : TRUE_COLOR, INDEXED');
				break;
		}
		$this->stack[] = array(__FUNCTION__, func_get_args() );
		return $pictures;	
	}	
	
	/**
     * Creation d'une ressource image gd
	 *
	 * @arguments 	STRING $pictures chemin d'acc�s � une image
	 * @return    	BOOL 0 si une erreur est survenuee
	 *				RESOURCE images
     */
	public function open( $pictures ) {
		if( !is_file($pictures) ) {	return FALSE; }
		
		$pictures_param = $this->infos( $pictures );
		
		switch( $pictures_param['mime'] ) {
			case 'image/png' :
				return imagecreatefrompng($pictures);
				break;
				
			case 'image/jpeg' :
				return imagecreatefromjpeg($pictures);
				break;
			
			case 'image/gif' :
				return imagecreatefromgif($pictures);
				break;
			
			default :
				return FALSE;
				break;
		}
	}
	
	/**
     * Retourne un nom de fichier $fichier sans son extension, utile pour PHP < 5.2.0
	 *
	 * @arguments 	STRING $fichier nom de fichier avec son extension
	 * @return    	STRING nom de fichier sans son extension
     */
	protected function name( $fichier ) {
		return basename ( $fichier, strrchr( $fichier, '.' ) );
	}
	
	/**
     * Renvoie les propri�t�s d'une image $pictures voir function getimagesize() 
	 * dans le manuel PHP
	 *
	 * @arguments 	STRING 	$pictures chemin d'acc�s � une image ou une ressource
	 * @return    	BOOL   	FALSE si une erreur est survenuee
	 * 						TRUE en cas de succ�s
     */
	protected function infos( $pictures ) {
		if( !is_resource($pictures) ) {
			return getimagesize( $pictures ) ;
		}
		else {
			$values["Width"]	= imagesx( $pictures );
			$values["Height"]	= imagesy( $pictures );
			return $values;
		}
		
	}
	
	/**
     * Copie une image $picturestomerge avec comme opacit� $picturestomergeopacity dans la ressource GD
	 * courante dans une zone pr�d�finie $flag avec une marge $margin , ou � l'endroit souhait� avec
	 * comme coordonn�es ($x_origin, $y_origin).
	 *
	 * @arguments 	STRING 	$picturestomerge l'image � inserer dans $pictures
	 *				INT 	$picturestomergeopacity 0 : opaque, 127 transparent
	 *				CONST	$flag voir set_crop_pics()
	 *				INT		$x_origin coordonn�e x de l'image � coller
	 *				INT		$y_origin coordonn�e y de l'image � coller
	 *				INT		$margin marge souhait�e en pixel
	 * @return    	BOOL   	1 en cas de succ�s
	 *					   
     */
	public function merge( $picturestomerge, $flag = CENTER, $picturestomergeopacity = 99, $x_origin = NULL, $y_origin = NULL, $margin = NULL ) {
		// var_dump($picturestomerge);
		if( is_object( $picturestomerge ) ) {
			$pictures			 = $picturestomerge->_gdPics ;
			$picsToMergeWidth	 = $picturestomerge->getWidth();
			$picsToMergeHeight	 = $picturestomerge->getHeight();
		}
		elseif( is_string( $picturestomerge ) ) {
			$temp				 = new Pics($picturestomerge);
			$pictures			 = $temp->_gdPics;
			$picsToMergeWidth	 = $temp->getWidth();
			$picsToMergeHeight	 = $temp->getHeight();
		}
		elseif( is_resource( $picturestomerge ) ) {
			$pictures 			 = $picturestomerge;
			$picsToMergeWidth	 = imagesx($picturestomerge);
			$picsToMergeHeight	 = imagesy($picturestomerge);
		}

		static $x ;
		static $y ;
		
		switch ($flag) {

			case TOP_LEFT:
				$x = 0 + $margin;
				$y = 0 + $margin ;
				break;
						
			case TOP:
				$x = ($this->getWidth() - $picsToMergeWidth) / 2;
				$y = 0 + $margin;
				break;
						
			case TOP_RIGHT:
				$x = ($dest_im[0] - $src_im[0]) - $margin;
				$y = 0 - $margin;
				break;

			case RIGHT:
				$x = ($dest_im[0] - $src_im[0]) - $margin;
				$y = ($dest_im[1] - $src_im[1]) / 2;
				break;
						
			case BOTTOM_RIGHT:
				$x = ($dest_im[0] - $src_im[0]) - $margin;
				$y = ($dest_im[1] - $src_im[1]) - $margin;
				break;
							
			case BOTTOM:
				$x = ($dest_im[0] - $src_im[0]) / 2;
				$y = ($dest_im[1] - $src_im[1]) - $margin;
				break;
						
			case BOTTOM_LEFT :
				$x = 0 + $margin;
				$y = ($dest_im[1] - $src_im[1]) - $margin;
				break;
				
			case LEFT :
				$x = 0 + $margin;
				$y = ($dest_im[1] - $src_im[1]) / 2;
				break;

			case CENTER :
				$x = floor( ( $this->getWidth() - $picsToMergeWidth ) / 2);
				$y = floor( ( $this->getHeight() - $picsToMergeHeight ) / 2);
				break;
				
			case CUSTOM :
				$x = $x_origin;
				$y = $y_origin;
				break;
				
			default :
				throw new Exception ('Bad $flag, try : TOP_LEFT, TOP, TOP_RIGHT, RIGHT, BOTTOM_RIGHT, BOTTOM, BOTTOM_LEFT, LEFT, CENTER, CUSTOM');
				break;			
		}
		
		imagecopymerge( $this->_gdPics, $temp->_gdPics, $x, $y, 0, 0, $picsToMergeWidth, $picsToMergeHeight , $picturestomergeopacity );
		
		$this->stack[] = array(__FUNCTION__, func_get_args() );
		
		return true;
	}
	
	/**
     * Redimensionne l'image courante, avec proportionalit� sur la hauteur $new_width, largeur $new_height
	 * ou selon une taille pr�d�finie ($new_width, $new_height)
	 *
	 * @arguments 	INT $new_width taille en pixel de la largeur voulue
	 *				INT $new_height taille en pixel de la hauteur voulue
	 *				
	 * @return    	BOOL 0 si une erreur est survenue
	 *				RESOURCE images
     */
	public function resize( $new_width = NULL, $new_height = NULL ) {
	
		if( $this->type == LOAD_FILE ) {
			$pics_param 		= self::infos( $this->src ) ;
			$pictures_origin	= self::open( $this->src ) ;
			// $pictures_origin	= $this->_gdPics ;
		}
		else {
			$pics_param[0] = $this->width ;
			$pics_param[1] = $this->height ;
			
		}
		
		// Resize fixed width and height
		if( isset( $new_width ) && isset( $new_height ) ) {
			$width  = $new_width;
			$height = $new_height;
		}
		// Resize by new width
		elseif( isset( $new_width ) && is_null( $new_height ) ) {
			// Ratio by width
			if( $new_width > $pics_param[0] ) {
				$ratio  = $new_width / $pics_param[0] ;
				$width  = $new_width ;
				$height = round( $pics_param[1] * $ratio ) ;
			}
			else {
				$ratio  = $pics_param[0] / $new_width  ;
				$width  = $new_width ;
				$height = round( $pics_param[1] / $ratio ) ;
			}
		}
		// Resize by new height
		elseif( isset( $new_height ) && is_null( $new_width ) ) {
			// Ratio by height
			if( $new_height > $pics_param[1] ) {
				$ratio  = $new_height / $pics_param[1] ;
				$width  = round( $pics_param[0] * $ratio ) ;
				$height = $new_height ;
			}
			else {
				$ratio  = $pics_param[1] / $new_height ;
				$width  = round( $pics_param[0] / $ratio ) ;
				$height = $new_height ;
			}
		}
		
		$image_mini 	= imagecreatetruecolor( $width, $height ) ;
		
		ImageAlphaBlending( $image_mini, false );
		ImageSaveAlpha( $image_mini, true );
		imagecopyresampled( $image_mini, $this->_gdPics, 0, 0, 0, 0, $width, $height, $pics_param[0], $pics_param[1] ) ;
		$this->_gdPics = $image_mini;
		unset($image_mini);
		$this->stack[] = array(__FUNCTION__, func_get_args());
		return TRUE;
	}
	
	/**
     * Rogne l'image courante de largeur $width_cropt et de hauteur $height_crop a l'endroit pr�d�finie $flag, ou selon les
	 * coordonn�es pr�definies ($x_origin, $y_origin) si $flag = custom
	 *
	 * @arguments 	STRING $image_saved nom de l'image � sauvegarder
	 *				INT $width_crop taille en pixel de la largeur voulue finale
	 *				INT $height_crop taille en pixel de la hauteur voulue finale
	 *				CONT $flag TOP_LEFT, TOP, TOP_RIGHT, RIGHT, BOTOM_RIGHT, BOTTOM, BOTTOM_LEFT, CENTER
	 *							CUSTOM
	 *				INT $x_origin Coordon�e de l'origin x si $flag = CUSTOM
	 *				INT $y_origin Coordon�e de l'origin y si $flag = CUSTOM
	 *				
	 * @return    	BOOL 1 En cas de succ�s sinon 0
     */
	public function crop( $width_crop, $height_crop, $flag = CENTER, $x_origin = '', $y_origin = '' ) {

		if( $this->type === LOAD_FILE ) {
			$pics_param = self::infos( $this->src ) ;
		}
		else {
			$pics_param[0] = $this->width ;
			$pics_param[1] = $this->height ;
		}
		
		// if( self::getPropreties( $this->_gdPics ) !== FALSE ) {
			// $pics_param = self::getPropreties( $this->_gdPics );
			if( $width_crop >= $pics_param[0] || $height_crop >= $pics_param[1] ) {
				return FALSE ;
			}
			else {
			
				$pictures_origin = self::open( $this->src ) ;
				$image_mini 	 = imagecreatetruecolor( $width_crop, $height_crop ) ;
				
				if( $x_origin != '' && $y_origin != '' && $flag = CUSTOM ) {
						ImageAlphaBlending( $image_mini, false );
						ImageSaveAlpha( $image_mini, true );
						imagecopyresampled( $image_mini, $this->_gdPics, 0, 0, $x_origin, $y_origin, $width_crop, $height_crop, $width_crop, $height_crop);
						$this->_gdPics = $image_mini;
				}
				else { 
					static $x;
					static $y;
					
					switch ($flag) {

						case TOP_LEFT:
						$x = 0;
						$y = 0;
							break;
							
						case TOP:
						$x = ($pics_param[0] - $width_crop) / 2;
						$y = 0;
							break;
						
						case TOP_RIGHT:
						$x = ($pics_param[0] - $width_crop);
						$y = 0;
							break;

						case RIGHT:
						$x = ($pics_param[0] - $width_crop);
						$y = ($pics_param[1] - $height_crop) / 2;
							break;
						
						case BOTTOM_RIGHT:
						$x = ($pics_param[0] - $width_crop);
						$y = ($pics_param[1] - $height_crop);
							break;
							
						case BOTTOM:
						$x = ($pics_param[0] - $width_crop) / 2;
						$y = ($pics_param[1] - $height_crop);
							break;
							
						case BOTTOM_LEFT :
						$x = 0;
						$y = ($pics_param[1] - $height_crop);
							break;
							
						case LEFT :
						$x = 0;
						$y = ($pics_param[1] - $height_crop) / 2;
							break;
							
						case CENTER :
						$x = ($pics_param[0] - $width_crop) / 2;
						$y = ($pics_param[1] - $height_crop) / 2;
							break;
						
						default :
						throw new Exception ('Bad $flag, try : TOP_LEFT, TOP, TOP_RIGHT, RIGHT, BOTTOM_RIGHT, BOTTOM, BOTTOM_LEFT, LEFT, CUSTOM');
							break;
					}
					
					ImageAlphaBlending( $image_mini, false );
					ImageSaveAlpha( $image_mini, true );
					imagecopyresampled( $image_mini, $this->_gdPics, 0, 0, $x, $y, $width_crop, $height_crop, $width_crop, $height_crop);
					$this->_gdPics = $image_mini;
					unset($image_mini);
				}
			}
		$this->stack[] = array( __FUNCTION__, func_get_args() );
		return TRUE;
	}
	
	/**
     * Applique un filtre $filtre sur l'image courante
	 *
	 * @arguments 	RESSOURCE $ressource une ressource image
	 *				CONST $filtre	IMG_FILTER_NEGATE		  Negatif de $pictures
	 *								IMG_FILTER_GRAYSCALE	  Supprime les couleurs
	 *								IMG_FILTER_BRIGHTNESS	  Luminosit� de l'image
	 *									INT $filtre_param_1   obligatoire 255 	: Eclaircir l'image avec un maximum vers le blanc (effet de brillance)
	 *																	0		: Couleurs inchang�es
	 *																	-255 	: Assombrir l'image au maximum vers le noir (effet sombre)
	 *								IMG_FILTER_CONTRAST		  Contraste de l'image
	 *									INT $filtre_param_1   obligatoire 255 	: Eclaircir l'image avec un maximum vers le blanc (effet de brillance)
	 *																	0		: Couleur inchang�es
	 *																	-255 	: Assombrir l'image au maximum vers le noir (effet sombre)
	 *								IMG_FILTER_COLORIZE		  Modifie les tendances de couleurs (RGB)
	 *									INT $filtre_param_1   obligatoire Valeur R [-255, 255]
	 *									INT $filtre_param_2   obligatoire Valeur G [-255, 255]
	 *									INT $filtre_param_3   obligatoire Valeur B [-255, 255]
	 *									INT $filtre_param_4   obligatoire Valeur Alpha [-255, 255]
	 *								IMG_FILTER_EDGEDETECT	  utilise la d�tection des bords pour les mettre en �vidence dans l'image.
	 *								IMG_FILTER_EMBOSS		  grave l'image en relief
	 *								IMG_FILTER_GAUSSIAN_BLUR  brouille l'image en utilisant la m�thode gaussienne. 
	 *								IMG_FILTER_SELECTIVE_BLUR brouille l'image
	 *								IMG_FILTER_MEAN_REMOVAL   son utilisation signifie le d�placement pour r�aliser un effet "peu pr�cis"
	 *								IMG_FILTER_SMOOTH		  rend l'image liss�e (smooth). Utilisez le param�tre args1  pour d�finir le degr� de lissoir [-8, 8]
	 *								IMG_FILTER_PIXELATE		  applique un effet de pixelisation � l'image; utilise arg1  pour indiquer la taille de bloc, et arg2  pour indiquer le mode de pixelisation. 
	 *				
	 * @return    	BOOL 0 si une erreur est survenue
	 *				RESOURCE images
     */
	public function filter( $filtre, $filtre_param_1 = '', $filtre_param_2 = '', $filtre_param_3 = '', $filtre_param_4 = '' ) {
		switch( $filtre ) {
					
			case IMG_FILTER_NEGATE :
			case IMG_FILTER_GRAYSCALE :
			case IMG_FILTER_EDGEDETECT :
			case IMG_FILTER_EMBOSS :
			case IMG_FILTER_GAUSSIAN_BLUR :
			case IMG_FILTER_SELECTIVE_BLUR :
			case IMG_FILTER_MEAN_REMOVAL :
				$this->stack[] = array( __FUNCTION__,func_get_args() );
				return imagefilter( $this->_gdPics, $filtre ) ;
				break;
				
			case IMG_FILTER_BRIGHTNESS :
			case IMG_FILTER_CONTRAST :
			case IMG_FILTER_SMOOTH :
				$this->stack[] = array( __FUNCTION__,func_get_args() );
				return  imagefilter( $this->_gdPics, $filtre, $filtre_param_1 );
				break;
				
			case IMG_FILTER_COLORIZE :
				$this->stack[] = array( __FUNCTION__,func_get_args() );
				return imagefilter( $this->_gdPics, IMG_FILTER_CONTRAST, $filtre_param_1, $filtre_param_2, $filtre_param_3, $filtre_param_4 );
				break;
				
			case IMG_FILTER_PIXELATE :
				$this->stack[] = array( __FUNCTION__,func_get_args() );
				return imagefilter( $this->_gdPics, IMG_FILTER_PIXELATE, $filtre_param_1, $filtre_param_2 );
				break;
				
			case IMG_FILTER_SEPIA :
				$this->stack[] = array( __FUNCTION__,func_get_args() );
				imagefilter( $this->_gdPics, IMG_FILTER_GRAYSCALE );
				return imagefilter( $this->_gdPics, IMG_FILTER_COLORIZE, 100, 50, 0);
				break;
				
			default :
				return $this->_gdPics;
				break;
		}
		
	}

	/**
     * Applique un masque d'opacit� $mask sur l'image courante
	 * Attention $mask DOIT �tre une image PNG avec canal ALPHA (png 24 bits)
	 *
	 * @arguments 	RESSOURCE $pictures chemin d'acc�s d'une image
	 * 						  $mask chemin d'acc�s d'une image png
	 *				CONST $flag RESIZE_TO_PICTURES_SIZE Les dimensions de $mask seront adapt�es aux dimensions de $pictures defaut
	 *							RESIZE_TO_MASK_SIZE     Les dimensions de $pictures seront adapt�es aux dimensions de $mask
	 *				
	 * @return    	BOOL 1 En cas de succ�s
	 *
	 * @throw EXCEPTION si $flag n'est pas reconnu
     */
	public function mask( $mask, $flag = RESIZE_TO_PICTURES_SIZE ) {
	
		if( ( self::open( $mask ) ) === FALSE ) {
			return FALSE;
		}
		
		else {
			switch ($flag) {
				case RESIZE_TO_PICTURES_SIZE :
					$srcpicsproperties = array($this->width, $this->height);
					$srcpics 		   = $this->_gdPics;
					imagealphablending ($srcpics, FALSE);					
					$srcoriginmask 	   = self::open ( $mask );
					$srcoriginmasksize = getimagesize( $mask );
					$srcmask 		   = imagecreatetruecolor ($srcpicsproperties[0], $srcpicsproperties[1]);
					imagealphablending ( $srcmask, FALSE );
					imagecopyresized ( $srcmask, $srcoriginmask, 0, 0, 0, 0, $srcpicsproperties[0], $srcpicsproperties[1], $srcoriginmasksize[0], $srcoriginmasksize[1]);
					break;
					
				case RESIZE_TO_MASK_SIZE :
					$srcpicsproperties = getimagesize( $mask );
					$srcpics 		   = self::resize($this->_gdPics, $srcpicsproperties[0], $srcpicsproperties[1]);
					$srcmask 		   = self::open( $mask );
					break;
					
				default :
					throw new Exception ('Bad $flag, try : RESIZE_TO_PICTURES_SIZE, RESIZE_TO_MASK_SIZE');
					break;
			}
		}
		
		for ($i=0; $i < $srcpicsproperties[0]; ++$i) {
			for ($j=0; $j < $srcpicsproperties[1]; ++$j) {
			$pxl_alpha = imagecolorsforindex ( $srcmask, imagecolorat ($srcmask, $i, $j));
			$pxl_color = imagecolorsforindex ( $srcpics, imagecolorat ($srcpics, $i, $j));
			$color 	= imagecolorallocatealpha (
					$srcpics,
					$pxl_color['red'],
					$pxl_color['green'],
					$pxl_color['blue'],
					$pxl_alpha['alpha']);
			imagesetpixel ($srcpics, $i, $j, $color);
			}
		}
		$this->stack[] = array( __FUNCTION__, func_get_args() );
		imagesavealpha ($srcpics, TRUE);
		$this->_gdPics = $srcpics;
		return TRUE;
	}
	
	/**
     * Applique un pattern (motif) sur toute la surface de l'image
	 * Attention $pattern DOIT �tre une image PNG avec canal ALPHA (png 24 bits)
	 *
	 * @arguments 	$pattern chemin d'acc�s d'une image PNG
	 *				
	 * @return    	BOOL 1 En cas de succ�s
	 *
	 * @throw EXCEPTION si $pattern n'est pas reconnu
     */
	public function pattern( $pattern ) {
		if( $this->type == LOAD_FILE ) {
			$pictures = $this->src ;
		}
		else {
			$pictures = $this->_gdPics ;
		}
		
		if( !is_file( $pattern ) ) {
			throw new Exception("Pattern $pattern not found.");
		}
		
		static $x = 0 ;
		static $y = 0 ;
		
		$propretiesSRC 		= array($this->width, $this->height);
		$propretiesPattern 	= $this->infos( $pattern );
		
		$nbrColumn 	= floor( $propretiesSRC[0] / $propretiesPattern[0] ) ;
		$nbrRow 	= floor( $propretiesSRC[1] / $propretiesPattern[1] ) ;
		
		try {
			$temp = self::open( $pattern );
		}
		catch (exception $e) {
			echo $e;
		}
		
		// Pour chaque ligne
		for( $i = 0 ; $i <= $nbrRow ; $i++ ) {

			for( $j = 0 ; $j <= $nbrColumn ; $j++ ) {
				imagecopy( $this->_gdPics, $temp, $x, $y, 0, 0, $propretiesPattern[0], $propretiesPattern[1] );
				$x += $propretiesPattern[0] ;
			}
			
			$x = 0;
			$y += $propretiesPattern[1] ;
		}
		$this->stack[] = array( __FUNCTION__, func_get_args() );
		return true;

	}

	/**
     * Affiche l'image courante dans le naviguateur
	 *
	 * @arguments 	CONST $flag PNG
	 *							JPEG
	 *							JPG (alias JPEG)
	 *							GIF
	 *				
	 * @return    	RESOURCE images
     */
	public function display( $flag = PNG ) {
		switch ( $flag ) {
			case PNG:
				header( 'Content-Type: image/' . $flag );
				imagepng( $this->_gdPics );
				imagedestroy( $this->_gdPics );
				break;
			
			case JPG:
				header( 'Content-Type: image/jpeg' );
				imagejpeg( $this->_gdPics );
				imagedestroy( $this->_gdPics );
				break;
				
			case JPEG:
				header('Content-Type: image/' . $flag);
				imagejpeg($this->_gdPics);
				imagedestroy($this->_gdPics);
				break;
				
			case GIF:
				header('Content-Type: image/' . $flag);
				imagegif($this->_gdPics);
				imagedestroy($this->_gdPics);
				break;
			
			default:
				header('Content-Type: image/' . $flag);
				imagepng($this->_gdPics);
				imagedestroy($this->_gdPics);
				break;
		}
	}

	/**
     * Sauvegarde de la ressource image courante _gdPics sous forme de fichier $savedpictures au format $format de de qualit� $quality
	 *
	 * @arguments 	CONST $format format de sortie de l'image, PNG, JPEG, JPG, GIF
	 *
	 *				STRING $savedpictures Chemin d'acc�s pour l'image de sortie 
	 *
	 *				INT $quality Qualit� de l'image de sortie (compression)
	 *						PNG : 0 pas de compression � 9
	 *						JPG : de 0 (pire qualit�, petit fichier) et 100 (meilleure qualit�, gros fichier)
	 *						GIF : NULL
	 *				
	 * @return    	BOOL 1 En cas de succ�s
	 *
	 * @throw EXCEPTION si $flag n'est pas reconnu
     */
	public function save( $format = PNG, $savedpictures = NULL, $quality = 5 ) {
		if( is_null( $savedpictures ) ) {
			throw new Exception ("Path $savedpictures is null, please specify target saved files.") ;
		}
		else {
			$saved_pics_name =  pathinfo( $savedpictures );
		}
		
		switch ( $format ) {
			case PNG :
				if( is_int( $quality ) && $quality >= 0 && $quality <= 9 ) {					
					imagepng($this->_gdPics, $saved_pics_name['dirname'] . '/' . self::name($savedpictures) . '.png', $quality);
					return TRUE;
				}
				else {
					return FALSE;
				}
				break;
				
			case JPG :
			case JPEG :
				if( is_int($quality) && $quality >= 0 && $quality <= 100 ) {
					imagejpeg($this->_gdPics, $saved_pics_name['dirname'] . '/' . self::name($savedpictures) . '.jpg', $quality);
					return TRUE;
				}
				else {
					return FALSE;
				}
				break;
				
			case GIF :
				if( imagegif( $this->_gdPics, $saved_pics_name['dirname'] . '/' . self::name($savedpictures) . '.gif' ) === TRUE ) {
					return TRUE;
				}
				else {
					return FALSE;
				}
				break ;
				
			default :
				throw new Exception ("Unknown format $format, please try PNG, JPG, GIF");
				
				break ;
		}
	}
	
	/**
     * Sauvegarde des differents traitement �ffectu�s sur l'image MAIS ne modifie pas l'image source.
	 *
	 * @arguments 	STRING $file Chemin d'un fichier pour sauvegarder les traitements 
	 *
	 *				
	 * @return    	BOOL 1 En cas de succ�s de la sauvegarde
	 *
	 * @throw EXCEPTION si $file n'est pas disponible en �criture
     */
	public function saveByActions( $file ) {
		if ( !is_writable( $file ) && !fopen( $file , "w" ) ) {
			throw new Exception("$file is not writable");
		}
		$string   = "";
		$compteur = 1;
				
		foreach( $this->stack as $value ) {
			$string.= serialize($value) . "\n";
			++$compteur;
		}
		
		$fp = fopen( $file, 'w+' );
		fwrite( $fp, utf8_encode ( $string ) );
		fclose( $fp );
		return TRUE;
	}
	
	/**
     * Charge une s�rie de traitement � �ffectu�s sur une image, ATTENTION le fichier sauvegard� DOIT
	 * �tre dans le m�me dossier que l'image source.
	 *
	 * @arguments 	STRING $path Chemin d'acc�s pour charger les traitements 
	 *
	 *				
	 * @return    	OBJECT Un nouvel objet Pics
	 *
     */
	public function loadByActions( $path ) {
	
		$lines = file($path);
		$compteur = 0;
		
		foreach ($lines as $content) {
			$temp = unserialize($content);
			// Juste le nom de la m�thode � appliqu�e
			$methode = $temp[0];
			array_shift($temp);

			// Si ce n'est pas le constructeur
			if( $compteur != 0 ) {
				call_user_func_array( array($ret, $methode),  $temp[0]);
			}
			// Si c'est le constructeur nous utilisons la reflectivit� pour passer des arguments � la m�thode __construct
			// chose impossible avec call_user_func_array()
			else {
				// Les arguments doivent �tre sous forme de chaine et non pas de tableau
				$callArgs = '';
				foreach($temp as $args) {
					$callArgs .= $args;
				}
				$reflect	= new ReflectionClass( get_class() );
				$ret		= $reflect->newInstanceArgs( $args );
			}
			$compteur++;
		}
		
		return $ret;
	}	

	/**
     * Vide la pile de traitement de la ressource courante
	 */
	public function reset() {
		$this->stack = array();
	}

}
?>