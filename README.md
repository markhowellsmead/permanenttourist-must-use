# WordPress Site Blocks

## Description

This WordPress plugin is intended for use as a block registration plugin. By building and compiling blocks through this plugin, and not in the Theme, you can ensure that the blocks remain available if you choose to switch to another Theme.

## Usage

1. Install this as a plugin in your local WordPress installation.
2. Run `npm install` in the plugin root folder. You'll need to be using the versions of Node and NPM specified in the `engines`section of package.json.
3. Run `npm start` in the plugin root folder. Then add the blocks in the _assets/src/blocks/_ folder. Webpack will watech this folder for changes and build a single output file, which is enqueued in the block editor.
4. This plugin doesn't currently load, handle or compile any CSS. That belongs in the Theme.

## Author

Mark Howells-Mead | www.permanenttourist.ch | Since 1st February 2022

## License

Please respect the GPL v3 licence, which is available via http://www.gnu.org/licenses/gpl-3.0.html
