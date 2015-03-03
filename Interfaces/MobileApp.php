<?php
/**
 * MobileApp interface
 * 
 * While NOLOH works for both mobile and non-mobile devices alike, sometimes, a developer wants to take the extra step
 * to explicitly create a drastically different application for mobile devices. The MobileApp interface allows you to do 
 * this by indicating that a WebPage class, if it implements it, is inteded to be a mobile-only app. 
 * This has a number of ramifications, both actual and semantic, from their non-mobile counterparts.
 * This is often (but not always) used together with Configuration's MobileAppURL property.
 * 
 * @package Interfaces
 */
interface MobileApp
{
	
}
?>