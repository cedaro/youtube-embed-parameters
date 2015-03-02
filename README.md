# YouTube Embed Parameters

Customize parameters for embedded Youtube players, including oEmbeds.

__Contributors:__ [Brady Vercher](https://twitter.com/bradyvercher)  
__Requires:__ 4.0  
__Tested up to:__ 4.1  
__License:__ [GPL-2.0+](http://www.gnu.org/licenses/gpl-2.0.html)

YouTube allows embedded players to be [customized by setting parameters](https://developers.google.com/youtube/player_parameters) in the iframe URL, however, custom parameters are stripped when passed through the oEmbed endpoint, making it difficult to customize the players. _YouTube Embed Parameters_ makes sure custom parameters can be added by filtering the oEmbed HTMl.

## Usage

Default parameters can be set on the _Settings &rarr; Media_ screen in the admin panel.

It's possible to override the defaults for individual players by adding parameters to the original URL. For example, when inserting a YouTube video URL into the editor, add `&rel=0` to the end of the URL to disable related videos for that particular player.

## Installation

### Upload

1. Download the [latest release](https://github.com/cedaro/youtube-embed-parameters/archive/master.zip) from GitHub.
2. Go to the _Plugins &rarr; Add New_ screen in your WordPress admin panel and click the __Upload__ button at the top next to the "Add Plugins" title.
3. Upload the zipped archive.
4. Click the __Activate Plugin__ link after installation completes.

### Manual

1. Download the [latest release](https://github.com/cedaro/youtube-embed-parameters/archive/master.zip) from GitHub.
2. Unzip the archive.
3. Copy the folder to `/wp-content/plugins/`.
4. Go to the _Plugins &rarr; Installed Plugins_ screen in your WordPress admin panel and click the __Activate__ link under the _YouTube Embed Parameters_ item.

Read the Codex for more information about [installing plugins manually](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

### Git

Clone this repository in `/wp-content/plugins/`:

`git clone git@github.com:cedaro/youtube-embed-parameters.git`

Then go to the _Plugins &rarr; Installed Plugins_ screen in your WordPress admin panel and click the __Activate__ link under the _YouTube Embed Parameters_ item.
