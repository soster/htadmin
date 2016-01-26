# Puppet configurations


Exec { path =>  [ "/bin/", "/sbin/" , "/usr/bin/", "/usr/sbin/" ] }

class base {

  ## Update apt-get ##
  exec { 'apt-get update':
    command => '/usr/bin/apt-get update'
  }
}

class apache 
{      
    package 
    { 
        "apache2":
            ensure  => present,
            require => Exec['apt-get update']
    }
    
    service 
    { 
        "apache2":
            ensure      => running,
            enable      => true,
            require     => Package['apache2'],
            subscribe   => [
                File["/etc/apache2/mods-enabled/rewrite.load"],
                File["/etc/apache2/sites-available/000-default.conf"]
            ],
    }

    file 
    { 
        "/etc/apache2/mods-enabled/rewrite.load":
            ensure  => link,
            target  => "/etc/apache2/mods-available/rewrite.load",
            require => Package['apache2'],
    }

    file 
    { 
        "/etc/apache2/sites-available/000-default.conf":
            ensure  => present,
            source  => "/vagrant/puppet/templates/vhost",
            require => Package['apache2'],
    }
}

class php{

  package { "php5":
    ensure => present,
  }

  package { "php5-cli":
    ensure => present,
  }

  package { "php5-xdebug":
    ensure => present,
  }-> file 
    { 
        "/etc/php5/mods-available/xdebug.ini":
            ensure  => present,
            source  => "/vagrant/puppet/templates/xdebug",
            require => Package['php5-xdebug'],
    }

  package { "php5-mysql":
    ensure => present,
  }


  package { "php5-mcrypt":
    ensure => present,
  }

  package { "php-pear":
    ensure => present,
  }

  package { "php5-dev":
    ensure => present,
  }

  package { "php5-curl":
    ensure => present,
  }


  package { "libapache2-mod-php5":
    ensure => present,
  }
  
}

class mysql{

  package { "mysql-server":
    ensure => present,
  }

  service { "mysql":
    ensure  => running,
    require => Package["mysql-server"],
    notify  => Exec["set-mysql-password"],
  }

  exec { "set-mysql-password":
    command => "mysqladmin -u root password root",
  }
}


include base
include apache
include php


# We don't need mysql right now:
# include mysql

