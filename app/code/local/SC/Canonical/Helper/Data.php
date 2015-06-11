<?php
/*
 * @category   SC
 * @package    SC_Canonical
 * @author     Simone Cabrino
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class SC_Canonical_Helper_Data extends Mage_Core_Helper_Abstract
{
     public function getHeadCanonicalUrl()
     {
         if (empty($this->_data['urlKey']))
         {
             $url = Mage::helper('core/url')->getCurrentUrl();
             $parsedUrl = parse_url($url);
             $scheme = $parsedUrl['scheme'];
             $host = $parsedUrl['host'];
             $port = isset($parsedUrl['port']) ? $parsedUrl['port'] : null;
             $path = $parsedUrl['path'];
             $headUrl = $scheme . '://' . $host . ($port && '80' != $port ? ':' . $port : '') . $path;
             
             if (!preg_match('/\.(rss|html|htm|xml|php?)$/', strtolower($headUrl)) && substr($headUrl, -1) != '/')
             {
                 $headUrl .= '/';
             }
             $this->_data['urlKey'] =$headUrl;
         }
         return $this->_data['urlKey'];
     }

     public function getHeadProductCanonicalUrl()
     {  
          $product_id =  Mage::app()->getRequest()->getParam('id');
          $_product = Mage::getModel('catalog/product')->load($product_id);
          $selected = $_product->getData('sc_canonicalurl');
          if($selected!=NULL)
          {
              if($selected==1)
              {
                  $product_id =  Mage::app()->getRequest()->getParam('id');
                  $_item = Mage::getModel('catalog/product')->load($product_id);
                  $this->_data['urlKey'] = Mage::getBaseUrl().$_item->getUrlKey().Mage::helper('catalog/product')->getProductUrlSuffix();
                  if (!preg_match('/\.(rss|html|htm|xml|php?)$/', strtolower($this->_data['urlKey'])) && substr($this->_data['urlKey'], -1) != '/')
                  {
                      $this->_data['urlKey'] .= '/';
                  }
                  return trim($this->_data['urlKey']);
              }
              else
              {
                  $base_url = Mage::getBaseUrl();
                  return trim($base_url . $selected);
              }
          }
          else
          {
               if (empty($this->_data['urlKey']))
               {
                   $url = '';
                   $base_url = Mage::getBaseUrl();
                   $product_id =  Mage::app()->getRequest()->getParam('id');
                   $_product = Mage::getModel('catalog/product')->load($product_id);
                   $catIds = $_product->getCategoryIds();
                   foreach ($catIds as $key ) 
                   {
                       $catCollection = Mage::getResourceModel('catalog/category_collection')->addAttributeToFilter('entity_id', $key);
                       foreach($catCollection as $cat)
                       {
                           $_category = Mage::getModel('catalog/category')->load($cat->getId());
                           $url = $base_url . $_product->getUrlPath($_category);
                           if(strlen($this->_data['urlKey']) < strlen($url)) {
                               $this->_data['urlKey'] = $url;
                           }
                       }
                   }
                   
                   if (!preg_match('/\.(rss|html|htm|xml|php?)$/', strtolower($this->_data['urlKey'])) && substr($this->_data['urlKey'], -1) != '/')
                   {
                       $this->_data['urlKey'] .= '/';
                   }
               }
               return trim($this->_data['urlKey']);
          }
     }
}