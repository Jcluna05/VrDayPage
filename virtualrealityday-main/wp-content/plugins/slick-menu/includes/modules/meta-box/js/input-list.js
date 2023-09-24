jQuery( function( $ )
{
  function update()
  {
    var $this = $( this ),
      $children = $this.closest( 'li' ).children('ul');

    if ( $this.is( ':checked' ) )
    {
      $children.removeClass( 'hidden' );
    }
    else
    {
      $children
        .addClass( 'hidden' )
        .find( 'input' )
        .removeAttr( 'checked' );
    }
  }

  $( '.sm-rwmb-input' )
    .on( 'change', '.sm-rwmb-input-list.collapse :checkbox', update )
    .on( 'clone', '.sm-rwmb-input-list.collapse :checkbox', update );
  $( '.sm-rwmb-input-list.collapse :checkbox' ).each( update );
} );
