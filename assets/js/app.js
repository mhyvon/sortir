/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');

// $(document).ready(function() {
//     $('[data-toggle="popover"]').popover();
// });

// require the JavaScript
require('bootstrap-star-rating');
// require 2 CSS files needed
require('bootstrap-star-rating/css/star-rating.css');
require('bootstrap-star-rating/themes/krajee-svg/theme.css');

$('#sortie_ville').change(function(){
    $.ajax({
        url: '/lieu/ajaxAction',
        data: { villeid: $('#sortie_ville').val() },
        dataType: 'JSON',
        success: function(json){
            $('#sortie_lieu').empty();
            let liste = JSON.parse(json);
            for (let i = 0; i<liste.length; i++){
                let o = new Option(liste[i].nom, liste[i].id);
                $(o).html(liste[i].nom);
                $('#sortie_lieu').append(o);
            }
        }
    })
});

$('#sortie_lieu').change(function () {
    $.ajax({
        url: '/ville/ajaxAction',
        data: { lieuid: $('#sortie_lieu').val() },
        dataType: 'JSON',
        success: function(json){
            let ville = JSON.parse(json);
            let idVille = ville.id;
            $('#sortie_ville option[value='+idVille+']').prop('selected', true);
            //$("#sortie_ville").trigger('change');

        }
    })
});

$(document).ready(function(){
    $("#modalBtn").click(function(){
        $("#exampleModal").modal();
    });
});


$('#nouveauLieu').submit(function(event) {

    event.preventDefault();  // Empêcher le rechargement de la page.

    var formData = new FormData($('#nouveauLieu').get(0))

    $.ajax({
        url: "/lieu/ajaxModale",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(){
            $("#exampleModal").modal('hide');
        }
    });
});

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})

