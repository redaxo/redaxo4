<?php
/**
 * Abstract OOFilter Class provides some SQL-Base filters 
 * 
 * @author Markus Staab
 * @link http://www.public-4u.de www.public-4u.de
 * @since OOF3.0 - 06.05.2005
 * @abstract
*/


class OOFilter {
    var $additionalFilter = null;
    var $operator = null;
    
    function OOFilter() {
        trigger_error('abstract class use - classbase OOFilter only available for subclassing!', E_USER_ERROR);
        exit();
    }
    
    function filterSQL() {
        trigger_error('abstract method use - method OOFilter::filter() have to be overridden!', E_USER_ERROR);
        exit();
    }
    
    function filter() {
        $sql = '';
        $sql .= $this->getOperator();
        $sql .= $this->filterSQL();
        
        if ( $this->hasFilter()) {
            if ( !( $sql .= $this->getFilter()->filter())) {
                return false;
            }
        }
        
        return $sql;
    }
    
    function addFilter( $additionalFilter, $operator = true) {
        if ( !$this->_isValidOperator( $operator)) {
            return false;
        }
        if ( !$this->isValidFilter( $additionalFilter)) {
            return false;
        }
        
        if ( is_null( $this->additionalFilter)) {
            $this->additionalFilter = $additionalFilter;
            $this->operator = $operator;
        } else {
            return $this->additionalFilter->addFilter( $additionalFilter, $operator);
        }
        
        return true;
    }
    
    function _isValidFilter($filter) {
        return is_a( $filter, 'oofilter');
    }
    
    function _isValidOperator( $operator) {
        return is_bool( $operator);
    }
    
    function getOperator() {
        return $this->operator ? ' AND ' : ' OR '; 
    }
    
    function getFilter() {
        return $this->additionalFilter;
    }
    
    function hasFilter() {
        return !is_null( $this->getFilter());
    }
    
    function invalidArg( $argName, $argValue, $expected) {
        trigger_error( 'Unexpected argument-value for '. $argName .'!<br/> => "'. $argValue .'"<br/> Expecting '. $expected .'!', E_USER_ERROR);
        exit();
    }
}

class ArticleFilter extends OOFilter {
    var $articleId;
    var $reverse;
    
    function ArticleFilter( $articleId, $reverse = false) {
        if ( !$this->_isValid( $articleId)) {
            $this->invalidArg( 'articleId', $articleId, 'int or int[]');
        }
        if ( !is_bool( $reverse)) {
            $this->invalidArg( 'reverse', $reverse, 'boolean value');
        }
        $this->articleId = $articleId;
        $this->reverse = $reverse;
    }
    
    function filterSQL() {
        $sql .= 'rex_article.article_id';
        
        if ( is_array( $this->articleId)) {
            if ( $this->reverse) {
                $sql .= ' NOT';
            }
            $sql .= ' IN ( ' . implode( ',', $this->articleId) . ')';
        } else {
            if ( $this->reverse) {
                $sql .= ' != ';
            } else {
                $sql .= ' = ';
            }
            $sql .= $this->articleId;
        }
        
        return $sql;
    }
    
    function _isValidId() {
    }
}
?>