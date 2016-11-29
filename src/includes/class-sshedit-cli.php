<?php
/**
 * The CLI Shell.
 *
 * @package SSH_Edit
 *
 * @since 1.0.0
 */
namespace SSHEdit;

/**
 * The CLI shell.
 *
 * The primary interface for SSH Edit.
 *
 * @api
 *
 * @since 1.0.0
 */
class CLI extends Shell {
	/**
	 * The "host" to display before the command prompt.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const NAME = 'sshedit';

	/**
	 * The "path" to display alongside the "host".
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $path = array();

	/**
	 * The config file currently being edited.
	 *
	 * @since 1.0.0
	 *
	 * @var Config
	 */
	protected $config = null;

	/**
	 * The ID of the current Section being edited.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $section;

	/**
	 * The ID of the current Alias being edited.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $alias;

	/**
	 * Initailize the CLI.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file The file to load.
	 */
	public function __construct( $file = null ) {
		echo "Welcome to SSH Edit. Type \"help\" for options, or \"quit\" to exit.\n";

		if ( $file && file_exists( $file ) ) {
			$this->config = new Config( $file );
		} else {
			echo "No file available for editing, please use the \"open\" command.\n";
		}

		parent::__construct();
	}

	/**
	 * Update the path before each loop begins.
	 *
	 * @since 1.0.0
	 */
	protected function before_loop() {
		$this->path = array( $this->section, $this->alias );
	}

	/**
	 * Remove the " to " keyword before parsing the command.
	 *
	 * @since 1.0.0
	 *
	 * @param string $command The command string provided.
	 *
	 * @return array The command and arguments extracted.
	 */
	protected function parse_command( $command ) {
		$command = str_replace( ' to ', ' ', $command );

		return parent::parse_command( $command );
	}

	/**
	 * Ensure $config is set, print error otherwise.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Wether or not the config is set.
	 */
	protected function has_config() {
		if ( is_null( $this->config ) ) {
			echo "ERROR: No config file opened/loaded yet.\n";
			return false;
		}
		return true;
	}

	/**
	 * Check if there unsaved changes, confirm action.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Wether or not to abort the action.
	 */
	protected function is_unsaved() {
		if ( $this->config && $this->config->has_changed() ) {
			if ( ! $this->confirm( "You have unsaved changes. Discard?" ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Print the list of available commands.
	 *
	 * @since 1.0.0
	 */
	protected function cmd_help() {
		echo <<<HELP

open SOURCE
Open and parse a config file for editing.

list [SECTION [ALIAS]]
List entries/details for a section/alias.

select [section|alias] ID
Select a section/alias to use for subsequent commands.

add [section ID|alias ID [to SECTION]]
Add a section/alias. Will prompt for values.

edit [SECTION [ALIAS]]
Edit a section/alias. Will prompt for changes.

delete [SECTION [ALIAS]]
Delete a section/entry.

save [DESTINATION]
Save the compiled config file.

print [SECTION [ALIAS]]
Print out the compiled config file.

dump [SECTION [ALIAS]]
Dump the entire object for a section/alias.

quit
Take a wild guess.


HELP;
	}

	/**
	 * Open a config file for editing.
	 *
	 * @since 1.0.0
	 *
	 * @param $file The file to open.
	 */
	public function cmd_open( $file = null ) {
		if ( $this->is_unsaved() ) {
			return;
		}

		if ( ! $file ) {
			echo "Please specify a file to open.\n";
			return;
		}

		$file = str_replace( '~', $_SERVER['HOME'], $file );
		$file = realpath( $file );

		if ( file_exists( $file ) ) {
			$this->config = new Config( $file );
			echo "File loaded and ready for editing.\n";
			return;
		} else if ( is_writable( $file ) ) {
			$this->config = new Config( $file );
			echo "File open and ready for editing.\n";
			return;
		}

		echo "File not found and location not writable.\n";
		return;
	}

	/**
	 * Save the current config.
	 *
	 * @since 1.0.0
	 *
	 * @param $file The file to save to.
	 */
	public function cmd_save( $file = null ) {
		if ( ! $this->has_config() ) {
			return;
		}

		if ( ! is_writable( $file ) ) {
			echo "Unable to write to {$file}\n";
			return;
		}

		$this->config->save( $file );

		echo "Config saved to {$file}.\n";
	}

	/**
	 * Quit assuming no changes have been made.
	 *
	 * @since 1.0.0
	 */
	public function cmd_quit() {
		if ( $this->is_unsaved() ) {
			return;
		}

		parent::cmd_quit();
	}
}
