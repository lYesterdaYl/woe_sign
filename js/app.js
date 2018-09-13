"use strict";




get_current_width();



function get_current_width() {
    let header = document.getElementsByClassName('header');
    let header_width = header[0].clientWidth;
    let main = document.querySelector('.content');
    main.style.height = header_width + "px";

}