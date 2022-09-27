#!/bin/bash

install_facetwp() {
	if [ ! -d $WP_CORE_DIR/wp-content/plugins/facetwp ]; then
		echo "Cloning FacetWP: https://${GIT_TOKEN}@${FACET_REPO}"
		git clone -b master --single-branch https://${GIT_TOKEN}@${FACET_REPO} $PLUGINS_DIR/facetwp
	fi
	echo "Activating FacetWP"
	wp plugin activate facetwp --allow-root
}

# Install plugins
install_facetwp

# Install WPGraphQL and Activate
if ! $( wp plugin is-installed wp-graphql --allow-root ); then
	wp plugin install wp-graphql --allow-root
fi
wp plugin activate wp-graphql --allow-root

# activate the plugin
wp plugin activate wp-graphql-facetwp --allow-root

# Set pretty permalinks.
wp rewrite structure '/%year%/%monthnum%/%postname%/' --allow-root

# Export the db for codeception to use
wp db export "${DATA_DUMP_DIR}/dump.sql" --allow-root

# If maintenance mode is active, de-activate it
if $(wp maintenance-mode is-active --allow-root); then
	echo "Deactivating maintenance mode"
	wp maintenance-mode deactivate --allow-root
fi

chmod 777 -R .
chown -R $(id -u):$(id -g) .
