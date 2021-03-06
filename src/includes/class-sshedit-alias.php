<?php
/**
 * The Alias Model.
 *
 * @package SSH_Edit
 *
 * @since 1.0.0
 */
namespace SSHEdit;

/**
 * The Alias model.
 *
 * A representation of an ssh alias.
 *
 * @api
 *
 * @since 1.0.0
 */
class Alias extends Item {
	/**
	 * The host name for the connection.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $hostname;

	/**
	 * The identity file/key for the connection.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $identityfile;

	/**
	 * The username for the connection.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $user;

	/**
	 * The port for the connection.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	protected $port = 22;

	/**
	 * Compile into SSH config file format.
	 *
	 * @since 1.0.0
	 *
	 * @return string The formatted data.
	 */
	public function compile() {
		return
		"Host {$this->id}\n" .
		"# {$this->comment}\n" .
		"	HostName {$this->hostname}\n" .
		"	IdentityFile {$this->identityfile}\n" .
		"	User {$this->user}\n" .
		"	Port {$this->port}\n";
	}

	/**
	 * Dump the alias as an array.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $self Wether to dump the Alias attributes or Item properties.
	 *
	 * @return array The alias attributes in array form.
	 */
	public function dump( $self = false ) {
		if ( $self ) {
			return parent::dump( $self );
		}

		return array(
			'Host' => $this->id,
			'HostName' => $this->hostname,
			'IdentityFile' => $this->identityfile,
			'User' => $this->user,
			'Port' => $this->port,
		);
	}
}
