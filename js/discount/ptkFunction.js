jQuery.noConflict();
(function( $ ) {
    $(function() {

        /**Funcion que realiza la busqueda de los productos por su codigo
         -------------------------------------------------------------------------------------------------------------*/
        $('#sku').focusout(function () {
            $('#loading-mask').show();
            if ($(this).val().length > 1) {
                getInformacion ($(this));
            } else {
                $('#loading-mask').hide();
            }
        });
        /**Funcion que imprime el precio con el descuento
         -------------------------------------------------------------------------------------------------------------*/
        $('#percentage').keyup(function (event){
            this.value = (this.value + '').replace(/[^0-9]/g, '');
            var precio = $('#precio').val();
            var descuento = precio - (($(this).val() / 100) * precio);
            $('.contenedor-producto .descuento').html(window.currency + number_format(descuento,2,'.',','));
        });
        /** Funcion que valida el rango de fechas
         -------------------------------------------------------------------------------------------------------------*/
        $('.js-start,.js-end').change(function (){

            var fechaInicio = $('.js-start').val();
            var fechaFinal = $('.js-end').val();
            var elemento = $(this);
            if(fechaInicio.length > 5   && fechaFinal.length > 5 ){
                $('#loading-mask').show();
                $.ajax({
                    url: window.baseUrlDiscountDate,
                    dataType: 'json',
                    data: {from: fechaInicio, to : fechaFinal, form_key: $('input[name="form_key"]').val()},
                    type: "POST",
                    success: function (datos) {
                        if(datos.message.length > 0) {
                            elemento.val('').focus();
                            alert(datos.message)
                        }
                        $('#loading-mask').hide();
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(thrownError);
                        $('#loading-mask').hide();
                    }
                });
            }
        });
        /* Funcion que valida que solo se escriban valores numericos
        ------------------------------------------------------------------------------------------------------------- */
        $('.js-number').keyup(function (event){
            return event.charCode >= 48 && event.charCode <= 57
        });

        function getInformacion (elemento){

            $.ajax({
                url: window.baseUrlDiscount,
                dataType: 'json',
                data: {sku: elemento.val(), form_key: $('input[name="form_key"]').val()},
                type: "POST",
                success: function (datos) {
                    if(datos.length == 0){
                        alert('No fount product');
                        elemento.val('');
                    }else{
                        $('.contenedor-producto').remove();
                        $('.hor-scroll').append( '<div class="contenedor-producto"><div class="thumbnail"><img src="..."><h3></h3><h2></h2><h2 class="descuento"></h2></div></div></div>');
                        $('.contenedor-producto img').attr('src', datos.img);
                        $('.contenedor-producto h3').html(datos.name);
                        $('.contenedor-producto h2').html(datos.priceFormat);
                        $('#product_id').val(datos.id);
                        $('#precio').val(datos.price);

                    }
                    $('#loading-mask').hide();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError);
                    $('#loading-mask').hide();
                }
            });

        }


        function number_format(number, decimals, dec_point, thousands_sep) {
            number = (number + '')
                .replace(/[^0-9+\-Ee.]/g, '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function(n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + (Math.round(n * k) / k)
                            .toFixed(prec);
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
                .split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '')
                    .length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1)
                    .join('0');
            }
            return s.join(dec);
        }

    });
})(jQuery);
