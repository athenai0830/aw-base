/**
 * AW-Base Theme Customizer Live Preview JS
 */
( function( $ ) {

    // Helper: apply color variables based on pattern
    function applyColorPattern( pattern ) {
        // Here we define the root CSS variables for the preview locally
        // In reality, this sets style inline on body or root.
        let vars = {};
        switch ( pattern ) {
            case 'blacktan':
                vars = { '--bg-color': '#111111', '--text-color': '#ffffff', '--main-color': '#d1a166', '--accent-color': '#a87a41' };
                break;
            case 'chocotan':
                vars = { '--bg-color': '#3e2723', '--text-color': '#fff3e0', '--main-color': '#d7ccc8', '--accent-color': '#a1887f' };
                break;
            case 'cream':
                vars = { '--bg-color': '#fffdd0', '--text-color': '#5d4037', '--main-color': '#ffeb3b', '--accent-color': '#ffc107' };
                break;
            case 'bluetan':
                vars = { '--bg-color': '#37474f', '--text-color': '#eceff1', '--main-color': '#81d4fa', '--accent-color': '#4fc3f7' };
                break;
            case 'white':
                vars = { '--bg-color': '#ffffff', '--text-color': '#333333', '--main-color': '#eeeeee', '--accent-color': '#cccccc' };
                break;
            case 'original':
            default:
                vars = { '--bg-color': '#F5F6F7', '--text-color': '#333333', '--main-color': '#11114d', '--accent-color': '#c30e24' };
                break;
        }

        $.each(vars, function(k, v) {
            document.documentElement.style.setProperty(k, v);
        });
    }

    wp.customize( 'awbase_settings[color_pattern]', function( value ) {
        value.bind( function( newval ) {
            applyColorPattern( newval );
        } );
    } );

    wp.customize( 'awbase_settings[font_family]', function( value ) {
        value.bind( function( newval ) {
            let font = '';
            if(newval === 'meiryo') font = '"Meiryo", sans-serif';
            else if(newval === 'yugothic') font = '"Yu Gothic", "YuGothic", sans-serif';
            else font = '"Hiragino Kaku Gothic ProN", "Hiragino Sans", sans-serif';
            document.documentElement.style.setProperty('--font-family', font);
        } );
    } );

} )( jQuery );
