=== Plugin Name ===
Contributors: jordanlarrigan
Donate link: http://tagboard.com
Tags: Tagboard, Social, Hashtags, Posts, Embed, Tweets
Requires at least: 3.0.1
Tested up to: 3.9.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple way to embed your tagboard into your Wordpress blog. It uses a shortcode and supports embedding into Pages/Posts/Widgets.

== Description ==

[Tagboard](https://Tagboard.com/) allows users to display a hashtag and its posts from all the major social networks in one stream-lined embedded view. For FREE.  It uses a shortcode and supports embedding into Pages/Posts/Widgets.

**What is Tagboard:**

Tagboard is a tool for searching and displaying hashtag content, interacting with users, and participating in the conversation surrounding a hashtag. It allows you to customize a tagboard page, moderate the posts, and grow hashtag communities, all from a centralized and simple interface. Not only has Tagboard revolutionized the way we experience and display social posts, it also automatically aggregates social media posts from Twitter, Facebook, Instagram and others in a single view, all in near real-time.

**Interact with your community in one place:**

Whether you're embedding tagboard on your blog or website, or you're using the Tagboard website, the community interaction tools are key. From your tagboard page, you can favorite, retweet, reply, comment and like posts in your stream without ever leaving the page. It saves you from having to log in and out of each social network just to interact with your community. 

**Experience #SafeSocial with moderation and curation tools:**

Hijacking, "bashtags", and obscenities are a natural part of social media and the internet nowadays. It's inevitable - but, that doesn't mean you can't avoid it. Tagboard lets you control the "social firehose" of unfiltered posts through our moderation (and/or curation) tools. These enable brands and organizations to safely host social media content on your website.

== Upgrade Notice ==

= 0.2 =
Update to support new Tagboard account based features


== Installation ==

####Basic Usage
Requirements: You must have a Tagboard account with embedding enabled. <a href="http://tagboard.com">http://tagboard.com</a>  

You should be able to use the shortcode in a widget, page or post by simply using the shortcode with the required id parameter. This parameter is the unique ID for your tagboard. This can be found on the Embed tab in your tagboard’s settings page. The resulting shortcode should look something like this.

`[tagboard id="WhyWorkAtTagboard/146433"]`

#### Advanced Usage
You are able to pass the advanced embed options via your shortcode. See the example below for the list of options you can choose from.
  
`[tagboard id="WhyWorkAtTagboard/146433" postlimit="10" mobilelimit="5" darkmode="true" fixedheight="true"]`

#### Defaults
You have the ability to choose the default embed settings by simply not adding them to your shortcode. These are the defaults.  

`
postlimit="50" 
mobilelimit="10" 
darkmode="false" 
fixedheight="false"
`

== Changelog ==
= 0.1 =
* Inital Release