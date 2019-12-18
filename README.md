# HubSpot to Wordpress Blog Migration Tool

This tool allows you to export all pages from your HubSpot blog into a JSON file, download all media, and then import into Wordpress.    

## Setup

- `composer install`
- Copy `.env.example` to `.env` and add your [HubSpot API key](https://knowledge.hubspot.com/integrations/how-do-i-get-my-hubspot-api-key?KBOpenTab).
 Wordpress API requires Basic Authentication, which can be achieved using the [Application Passwords](https://wordpress.org/plugins/application-passwords/) plugin.

## Usage

First, download all blog posts from HubSpot and export in JSON format.

    php bin/console hubspot:blog:export > blogs.json
    
Then, go through and download all images mentioned in the blog post or marked as the featured image. Create new blog post
export with updated paths ready for WordPress
    
    php bin/console hubspot:media:download blogs.json > blogs_with_updated_image_paths.json
    
Copy downloaded images into your Wordpress install

On Server

    mkdir wp-content/uploads/migrated

Locally:

    rsync -avz downloaded-images/ user@server:/var/www/html/wp-content/uploads/migrated/

Use WordPress API to create blog posts, specifying a default author name.
    
    php bin/console -vvv wordpress:blog:import blogs_with_updated_image_paths.json "Default Author Name"

## Assumptions Made

- All blog posts are imported as status "published" as we deleted anything that wasn't already published in HubSpot before starting.
- Pretty permalinks have been enabled, see here https://developer.wordpress.org/rest-api/#routes-endpoints (otherwise wp-json URL does not work)
- Any pre-existing WordPress users have their full name set as "Display name publicly as"
