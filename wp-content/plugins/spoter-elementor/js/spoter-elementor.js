

const mdpSpoterElementor = {
    spoterElementor: function ( wrapperName ) {
        const hotspots = document.querySelectorAll( `.${wrapperName} .mdp-spoter-elementor-hotspot-wrapper` );
        const hotspotsBox = document.querySelector( `.${wrapperName} .mdp-spoter-elementor-box` );
        const tooltipOpen = hotspotsBox.dataset.tooltipOpen;
        const tooltipClose = hotspotsBox.dataset.tooltipClose;

        // close on click outside
        if ( tooltipClose === 'on-click' || tooltipOpen === 'click' ) {
            document.addEventListener( 'click', e => {
                const tooltips = document.querySelectorAll( `.${wrapperName} .mdp-spoter-elementor-hotspot-tooltip-wrapper` );
                if ( !e.target.parentElement.classList.contains( 'mdp-spoter-elementor-hotspot' )
                    && !e.target.parentElement.parentElement.classList.contains( 'mdp-spoter-elementor-hotspot' )
                    && !e.target.parentElement.parentElement.classList.contains( 'mdp-spoter-elementor-hotspot-wrapper' )
                    && !e.target.parentElement.classList.contains( 'mdp-spoter-elementor-hotspot-tooltip' ) ) {
                    tooltips.forEach( tooltip => {
                        tooltip.classList.remove( 'mdp-spoter-elementor-hotspot-tooltip-show' );
                    } );
                }
            } );
        }

        // tooltip open
        hotspots.forEach( hotspot => {
            if ( tooltipOpen === 'hover' ) {
                const tooltip = hotspot.querySelector( '.mdp-spoter-elementor-hotspot-tooltip-wrapper' );
                hotspot.addEventListener( 'mouseenter', () => {

                    if ( !tooltip ) { return; }

                    if ( !tooltip.classList.contains( 'mdp-spoter-elementor-hotspot-tooltip-show' ) ) {
                        tooltip.classList.add( 'mdp-spoter-elementor-hotspot-tooltip-show' );
                    }
                } );

                if ( tooltipClose === 'on-leave' ) {
                    hotspot.addEventListener( 'mouseleave', () => {
                        tooltip.classList.remove( 'mdp-spoter-elementor-hotspot-tooltip-show' );
                    } );
                }
            } else if ( tooltipOpen === 'click' )  {
                hotspot.addEventListener( 'click', e => {
                    const tooltip = hotspot.querySelector( '.mdp-spoter-elementor-hotspot-tooltip-wrapper' );

                    if ( !tooltip ) { return; }

                    if ( e.target.classList.contains( 'mdp-spoter-elementor-hotspot-tooltip' ) || e.target.classList.contains( 'mdp-spoter-elementor-hotspot-tooltip-content' ) ) {
                        return;
                    }

                    if ( !tooltip.classList.contains( 'mdp-spoter-elementor-hotspot-tooltip-show' ) ) {
                        tooltip.classList.add( 'mdp-spoter-elementor-hotspot-tooltip-show' );
                    } else {
                        tooltip.classList.remove( 'mdp-spoter-elementor-hotspot-tooltip-show' );
                    }
                } );
            }
        } );
    }
}






/**
 * Init for Front-End
 * @param callback
 */
jQuery( window ).on( 'elementor/frontend/init', function () {

    elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function( $scope ) {

        if ( $scope[0].classList.contains( 'elementor-widget-mdp-spoter-elementor' ) ) {
            mdpSpoterElementor.spoterElementor.call( mdpSpoterElementor, `elementor-element-${$scope[0].dataset.id}` );
        }
    } );

} );