<?php
/**
 * List Helper
 *
 * @author Baruch Velez - baruchvelez@gmail.com
 *
 */
namespace Helpers;

/**
 * Create lists and navigations with ease
 */

class Lists{

  /**
   * open list
   *
   * creates a <ul> or <ol> tag
   *
   * @param array(id, class)
   * @param stirng (ul, ol) ul default
   *
   * @return string
   */
  public static function openList( $type = "ul", $attrs = [])
  {

    $o  = "<".$type." ";
    $o .= (isset($attrs['id'])) ? 'id="'.$attrs['id'].'" ': ' ';
    $o .= (isset($attrs['class'])) ? 'class="'.$attrs['class'].'" ': ' ';
    $o .= ">";
    return $o;
  }
  /**
   * close list
   * @param string (ul, ol) ul defaul
   */

  public static function closeList($type = "ul")
  {
    return "</".$type.">\n";
  }

  /**
   * open list item
   *
   * @param array (id, clas, role)
   *
   * @return string
   */
  public static function openItem($attrs = [])
  {
    $l  = "<li ";
    $l .= (isset($attrs['id'])) ? 'id="'.$attrs['id'].'" ': ' ';
    $l .= (isset($attrs['class'])) ? 'class="'.$attrs['class'].'" ': ' ';
    $l .= (isset($attrs['role'])) ? 'role="'.$attrs['role'].'" ': ' ';
    $l .= ">";
    return $l;
  }
  /**
   * close list item
   *
   * @return string
   */
  public static function closeItem()
  {
    return "</li>";
  }

  /**
   * creates a link
   *
   * @param string (text to be used in link)
   * @param string (link to location)
   * @param array  (id, class, target)
   *
   * @return string
   */
  public static function listLink($text,$href, $attrs = [])
  {
    $a  = "<a ";
    $a .= "href=".$href." ";
    $a .= (isset($attrs['id']) ? 'id="'.$attrs['id'].'" ' : " ");
    $a .= (isset($attrs['class']) ? 'class="'.$attrs['class'].'" ' : " ");
    $a .= (isset($attrs['target']) ? 'target="'.$attrs['target'].'" ' : " ");
    $a .= " >";
    $a .= $text;
    return $a;
  }
  /**
   * close link
   *
   * @return string
   */
   public static function closeLink()
   {
     return "</a>";
   }


   /**
    * Create a list item with custom text
    *
    * @param string item text
    * @param array options (id, class, role)
    *
    * @return string
    */
   public static function createitem( $t = "item",$attrs = [])
   {
     $o   =   self::openItem($lia);
     $o  .=   $t;
     $o  .=   self::closeItem();

     return $o;
   }

   /**
    * Create a list item with a link
    *
    * @param string link text
    * @param string link location
    * @param array optoins (id, class, target)
    *
    * @return string
    */
   public static function createNavitem($t="link", $h="#", $attrs=[])
   {
     $o   =   self::openItem($lia);
     $o  .=   self::listLink($t, $h, $attrs);
     $o  .=   self::closeLink();
     $o  .=   self::closeItem();

     return $o;
   }
}



 ?>
