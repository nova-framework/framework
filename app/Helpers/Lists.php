<?php
/**
 * List Helper
 *
 * @author Baruch Velez - baruchvelez@gmail.com
 *
 */
namespace Helpers;

/**
 * Create lists with ease
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
  public static function openList($attrs = [], $type = "ul")
  {

    $o  = "<".$type." ";
    $o .= (isset($attrs['id'])) ? 'id="'.$attrs['id'].'" ': ' ';
    $o .= (isset($attrs['class'])) ? 'class="'.$attrs['class'].'" ': ' ';
    $o .= ">";
    echo $o;
  }
  /**
   * close list
   * @param string (ul, ol) ul defaul
   */

  public static function closeList($type = "ul")
  {
    echo "</".$type.">\n";
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
    echo $l;
  }
  /**
   * close list item
   *
   * @return string
   */
  public static function closeItem()
  {
    echo "</li>";
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
  public static function listLink($text="link",$href="#", $attrs = [])
  {
    $a  = "<a ";
    $a .= "href=".$href." ";
    $a .= (isset($attrs['id']) ? 'id="'.$attrs['id'].'" ' : " ");
    $a .= (isset($attrs['class']) ? 'class="'.$attrs['class'].'" ' : " ");
    $a .= (isset($attrs['target']) ? 'target="'.$attrs['target'].'" ' : " ");
    $a .= " >";
    echo $a;
  }
  /**
   * close link
   *
   * @return string
   */
   public static function closeLink()
   {
     echo "</a>";
   }
}



 ?>
