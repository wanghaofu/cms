<?php


class Spring
{

	var $appcontext = array( );
	var $registerBeans = array( );

	function Spring( $_ClassPathResource )
	{
		if ( !file_exists( $_ClassPathResource ) )
		{
			$_ClassPathResource = ADMIN_PATH.$_ClassPathResource;
		}
		include( $_ClassPathResource );
		$this->appcontext = $_SPRING_APPCONTEXT;
		unset( $_SPRING_APPCONTEXT );
	}

	function &getInstance( $_ClassPathResource )
	{
		if ( isset( $GLOBALS['__Object_SpringBeanFactory'] ) && is_object( $__Object_SpringBeanFactory ) && get_class( $__Object_SpringBeanFactory ) == "spring" )
		{
			return $__Object_SpringBeanFactory;
		}
		else if ( isset( $GLOBALS['BeanFactory'] ) && is_object( $BeanFactory ) && get_class( $BeanFactory ) == "spring" )
		{
			return $BeanFactory;
		}
		else
		{
			$GLOBALS['GLOBALS']['__Object_SpringBeanFactory'] = new Spring( $_ClassPathResource );
			return $GLOBALS['GLOBALS']['__Object_SpringBeanFactory'];  // 新的
		}
	}

	function &getBean( $_bean )
	{
		$_bean_instance_name = "_SPRING_INSTANCE_".$_bean;
		if ( isset( $GLOBALS[$_bean_instance_name] ) )
		{
			return $GLOBALS['GLOBALS'][$_bean_instance_name];
		}
		else
		{
			if ( isset( $this->appcontext['beans'][$_bean], $this->appcontext['beans'][$_bean]['class'] ) )
			{
				import( $this->appcontext['beans'][$_bean]['class'] );
				$class_name = substr( strrchr( $this->appcontext['beans'][$_bean]['class'], "." ), 1 );
				if ( isset( $this->appcontext['beans'][$_bean]['constructor-arg'] ) )
				{
					$GLOBALS['GLOBALS'][$_bean_instance_name] = new $class_name( $this->appcontext['beans'][$_bean]['constructor-arg'] );
				}
				else
				{
					$GLOBALS['GLOBALS'][$_bean_instance_name] = new $class_name( );
				}
			}
			else if ( isset( $this->appcontext['beans'][$_bean]['class_path'] ) )
			{
				require_once( $this->appcontext['beans'][$_bean]['class_path'] );
				$class_name = $this->appcontext['beans'][$_bean]['class_name'];
				if ( isset( $this->appcontext['beans'][$_bean]['constructor-arg'] ) )
				{
					$GLOBALS['GLOBALS'][$_bean_instance_name] = new $class_name( $this->appcontext['beans'][$_bean]['constructor-arg'] );
				}
				else
				{
					$GLOBALS['GLOBALS'][$_bean_instance_name] = new $class_name( );
				}
			}
			else
			{
				exit( "Spring Error: Bean( ".$_bean." ) define error!" );
			}
		}
		$this->registerBeans[] = $_bean;
		return $GLOBALS['GLOBALS'][$_bean_instance_name];
	}

	function &createBean( $_bean )
	{
		if ( isset( $this->appcontext['beans'][$_bean] ) )
		{
			if ( isset( $this->appcontext['beans'][$_bean]['class'] ) )
			{
				import( $this->appcontext['beans'][$_bean]['class'] );
				$class_name = substr( strrchr( $this->appcontext['beans'][$_bean]['class'], "." ), 1 );
				$returnBean = new $class_name( );
			}
			else if ( isset( $this->appcontext['beans'][$_bean]['class_path'] ) )
			{
				require_once( $this->appcontext['beans'][$_bean]['class_path'] );
				$class_name = $this->appcontext['beans'][$_bean]['class_name'];
				$returnBean = new $class_name( );
			}
			else
			{
				exit( "Spring Error: Bean( ".$_bean." ) define error!" );
			}
		}
		else
		{
			exit( "Spring Error: Bean( ".$_bean." ) does not exists!" );
		}
		return $returnBean;
	}

	function destoryBean( $_bean )
	{
		$_bean_instance_name = "_SPRING_INSTANCE_".$_bean;
		if ( isset( $GLOBALS[$_bean_instance_name] ) )
		{
			unset( $GLOBALS[$_bean_instance_name] );
		}
	}

}

?>
