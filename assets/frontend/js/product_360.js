$(function () {
    //Load the image URLs into an Array.
    var arr = new Array();
    $("#pro360images img").each(function () {
        arr.push($(this).attr("src"));
    });

    //Set the first image URL as source for the Product.
    $("#product_360").attr("src", arr[0]);

    //MouseMove mode.
    $("#product_360").threesixty({ images: arr,
        method: 'click', //click, mousemove, auto
        sensibility: 1
    });
});