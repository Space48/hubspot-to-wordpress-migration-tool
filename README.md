# HubSpot to Wordpress Blog Migration Tool

This tool allows you to export all pages from your HubSpot blog into a JSON file, then import into Wordpress.

## Things to note

- There are two blogs in Space 48 HubSpot "Blog" and "Downloads" so we may need to
take this into consideration when importing into wordpress.    

## Setup

- `composer install`
- Copy `.env.example` to `.env` and add your API key found in [HubSpot Admin](https://app.hubspot.com/api-key/2805713)


## Usage

First, download all blog posts from HubSpot and export in JSON format.

    php bin/console hubspot:blog:export > blogs.json
    
Then, go through and download all images mentioned in the blog post or marked as the featured image. Create new blog post
export with updated paths ready for WordPress
    
    php bin/console hubspot:media:download blogs.json > blogs_with_updated_image_paths.json
    
Use WordPress API to create blog posts 
    
    php bin/console wordpress:blog:import blogs_with_updated_image_paths.json
    
(manully copy images in downloaded-images folder into wp-content/uploads/migrated/ of your install)
    