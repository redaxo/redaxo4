<?php

/**
 * OOTemplate Class provides some templates which are needed most projects
 * 
 * @author Markus Staab
 * @link http://www.public-4u.de www.public-4u.de
 * @since OOF3.0 - 24.04.2005
*/
    
class OOTemplate {
    /**
     * Returns the Default-Navigation or <code>false</code> on error
     * 
     * @access public
     * @return string or false on error
     */
    function getNavigation() {
        return OOTemplate::getListNavigation();
    }
    
    /**
     * Returns a <code>&lt;UL&gt;</code> & <code>&lt;LI&gt;</code> Styled-Navigation
     * or <code>false</code> on error
     * 
     * @access public
     * @return string or false on error
     */
    function getListNavigation() {
        return OOTemplate::getStructure( 'ul', 'navigation', 'li');
    }
    
    /**
     * Returns a <code>&lt;DIV&gt;</code> Styled-Navigation
     * or <code>false</code> on error
     * 
     * @access public
     * @return string or false on error
     */
    function getDivNavigation() {
        return OOTemplate::getStructure( 'div', 'navigation');
    }
    
    /**
     * Returns the Default-Sitemap or <code>false</code> on error
     * 
     * The Sitemap contains all categories and articles.
     * 
     * @access public
     * @return string or false on error
     */
    function getSitemap() {
        return OOTemplate::getListSitemap();
    }
    
    /**
     * Returns a <code>&lt;UL&gt;</code> & <code>&lt;LI&gt;</code> Styled-Sitemap
     * or <code>false</code> on error.
     * 
     * The Sitemap contains all categories and articles.
     * 
     * @access public
     * @return string or false on error
     */
    function getListSitemap() {
        return OOTemplate::getStructure( 'ul', 'sitemap', 'li', '', false);
    }
    
    /**
     * Returns a <code>&lt;DIV&gt;</code> Styled-Sitemap
     * or <code>false</code> on error
     * 
     * The Sitemap contains all categories and articles.
     * 
     * @access public
     * @return string or false on error
     */
    function getDivSitemap() {
        return OOTemplate::getStructure( 'div', 'sitemap', '', '', false);
    }
    
    /**
     * Returns the String-Representation of the Site-Structure using the given tags/classes
     * or <code>false</code> on error
     * 
     * @access protected 
     * @param string tag used for elements having childs
     * @param string class used for elements having childs
     * @param string childtag used for child-elements [optional]
     * @param string childclass used for child-elements [optional]
     * @param boolean switch if articles should be ignored [optional]
     * @param boolean switch if offline-articles/categories should be ignored [optional]
     * @return string or false on error
     */
    function getStructure( $tag, $class, $childtag = '', $childclass = '', $ignore_articles = true, $ignore_offlines = true) {
        $s = '';
        
        if ( $childtag === '') {
            $childtag = $tag;
        }
        
        // Get Root-Categories
        $categories = OOCategory::getRootCategories( $ignore_offlines);
        
        if ( count( $categories) > 0) {
            $s .= "\n" .'<'. $tag . ($class != '' ? ' class="'. $class .'"' : '') .'>'. "\n";
            
            foreach ( $categories as $rootcat) {
                if ( !($s .= OOTemplate::_getStructureNodes( $rootcat, $tag, $class, $childtag, $childclass, $ignore_articles, $ignore_offlines))) {
                    return false;
                }
            }
            
            $s .= '</'. $tag .'>'. "\n";
        }
        
        return $s;
    }
    
    /**
     * Returns the String-Representation of a node of the Site-Structure
     * or <code>false</code> on error
     * 
     * @access private
     * @param OOCategory category which childs should be represented
     * @param string tag used for elements having childs
     * @param string class used for elements having childs
     * @param string childtag used for child-elements
     * @param string childclass used for child-elements
     * @param boolean switch if articles should be ignored [optional]
     * @param boolean switch if offline-articles/categories should be ignored [optional]
     * @param int level (depth) of the element [internal use only]
     * @return string or false on error
     */
    function _getStructureNodes( $category, $tag, $class, $childtag, $childclass, $ignore_articles = true, $ignore_offlines = true, $level = 0) {
        $s = '';
        
        if ( !OOTemplate::_isValidNode( $category)) {
            var_dump( 'nö');
            return false;
        }
        
        $articles = array();
        $childs = $category->getChildren( $ignore_offlines);
        if ( !$ignore_articles) {
            $articles = $category->getArticles();
            for ( $i = 0; $i < count( $articles); $i++) {
                if ( $articles[ $i]->isStartPage()) {
                    unset( $articles[ $i]);
                    break;
                }
            }
        }
//        var_dump( $category->getName() . ' ,Anzahl Kinder: ' .count( $childs) . ' ,Level: ' . $level);
        
        // Get Category-Childs
        if ( count( $childs) > 0 || count( $articles) > 0) {
            // Get Category-open-HTML itself
            if ( !( $s.= OOTemplate::_openNode( $category, $childtag, $childclass, '', "\n". '<'.$tag.'>' ."\n"))) {
                return false;
            }
            
            // Get all articles except the start article
            foreach ( $articles as $article) {
                if ( $article->isStartPage()) {
                    continue;
                }
                
                if ( !($s .= OOTemplate::_getStructureNode( $article, $childtag, $childclass))) {
                    return false;
                }
            }
            
            foreach ( $childs as $child ) {
                // Get Child-Categories-Childs
                if ( !($s .= OOTemplate::_getStructureNodes( $child, $tag, $class, $childtag, $childclass, $ignore_articles, $ignore_offlines, $level + 1))) {
                    return false;
                }
            }
            
            // Get Category-close-HTML itself
            if ( !( $s.= OOTemplate::_closeNode( $category, $childtag, $childclass, '</'.$tag.'>'. "\n", ''))) {
                return false;
            }
        } else {
            // Get Category-HTML itself
            if ( !($s .= OOTemplate::_getStructureNode( $category, $childtag, $childclass))) {
                return false;
            }
        }
        
        return $s;
    }
    
    /**
     * Returns the tags for opening a node of the Site-Structure
     * or <code>false</code> on error
     * 
     * @access private
     * @return string or false on error
     */
    function _openNode( $node, $tag, $class, $prefix = '', $suffix = '') {
        if ( !OOTemplate::_isValidNode( $node)) {
//            var_dump( 'nö');
            return false;
        }
        
        $class = $class != '' ? ' class="'. $class .'"' : '';

        $s = '';
        $s .= $prefix;
        $s .= '<'. $tag . $class .'>';
        $s .= '<a href="'. $node->getUrl() .'">'. $node->getName() .'</a>';
        $s .= $suffix;
        
        return $s;
    }
    
    /**
     * Returns the tags for closing a node of the Site-Structure
     * or <code>false</code> on error
     * 
     * @access private
     * @return string or false on error
     */
    function _closeNode( $node, $tag, $class, $prefix = '', $suffix = '') {
        if ( !OOTemplate::_isValidNode( $node)) {
//            var_dump( 'nö');
            return false;
        }
        
        $s = '';
        $s .= $prefix;
        $s .= '</'. $tag .'>'. "\n";
        $s .= $suffix;
//        var_dump( $s);
        return $s;
    }
    
    /**
     * Returns the tags for a whole node of the Site-Structure
     * or <code>false</code> on error
     * 
     * @access private
     * @return string or false on error
     */
    function _getStructureNode( $node, $tag, $class) {
        if ( !OOTemplate::_isValidNode( $node)) {
//            var_dump( 'nö');
            return false;
        }
        
        $s = '';
        
        if ( !($s .= OOTemplate::_openNode( $node, $tag, $class))) {
            return false;
        }
        if ( !($s .= OOTemplate::_closeNode( $node, $tag, $class))) {
            return false;
        }
        
        return $s;
    }
    
    /**
     * Returns <code>true</code> if the given node is valid
     * 
     * @access private
     * @return boolean
     */
    function _isValidNode( $node) {
        return is_a($node, 'oocategory') || is_a($node, 'ooarticle');
    }
}

?>