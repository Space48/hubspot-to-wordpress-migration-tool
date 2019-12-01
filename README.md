# HubSpot Blog Post Exporter

When migrating away from HubSpot, the export of blog posts available from the 
admin interface provides full HTML pages. 

However, using the HubSpot API enables you to download just the blog post content.  

## Things to note

- Currently this repo just var dumps the blog posts out until we know what format is
useful for importing into Wordpress.
- Referenced images are still hosted on HubSpot so these will need to be downloaded 
and the URLs in the posts updated to match their new location.
- There are two blogs in Space 48 HubSpot "Blog" and "Downloads" so we may need to
take this into consideration when importing into wordpress.    

## Setup

- `composer install`
- Copy `.env.example` to `.env` and add your API key found in [HubSpot Admin](https://app.hubspot.com/api-key/2805713)


## Usage

    php bin/console hubspot:blog:export