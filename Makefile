.PHONY: examples

all:
	@echo
	@echo "Command       : Description"
	@echo "------------- : ---------------------"
	@echo "make composer : Download the composer tools"
	@echo "make install  : Install the development vendors and assets by composer"
	@echo "make examples : Run the examples"
	@echo

composer:
	@curl -sS https://getcomposer.org/installer | php

install:
	@php composer.phar install

examples:
	@php -S localhost:8080 -t examples
