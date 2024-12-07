let pointerNumber = 0;
$mo =jQuery;

$mo(window).load( function() {
    if(!moTour.tourTaken) {
        startTour(pointerNumber);
    }
    /** Restart tour for current page when clicked on the Restart button */
    $mo("#restart_tour_button").click( function() {
        resetTour();
        startTour(pointerNumber);
     });
});

/**
 * This function calls the functions that add overlay and create the cards.
 * @param pointerNumber int value
 */
function startTour(pointerNumber){
    if(!moTour.tourData) return;

    //checking if user is on any of the addons settings page, if yes then return.
    if(Object.keys(moTour.currentPage).length>1) return;

    $mo("#moblock").show();
    createCard(pointerNumber);
}

/**
 * This function creates the cards and adds them on a calculated position
 * @param pointerNumber
 */
function createCard(pointerNumber) {
    let tourElement =   moTour.tourData[pointerNumber];

    //added to hide cards if the next target is hidden and *tourElement.targetE != ''* is allowing cards to be created only for Form tab
    if(!$mo('#'+tourElement.targetE).is(':visible') && (pointerNumber!=0 || tourElement.targetE != '')){ 
        pointerNumber++; 
        if(moTour.tourData[pointerNumber] && $mo('#'+tourElement.targetE).is(':visible'))
             createCard(pointerNumber);  
            else {
                resetTour();
                tourComplete();
                return;
            }
        }


    let card        =   '<div id="mo-card" class="mo-card mo-'+tourElement.cardSize+'">'+
                            '<div class="mo-tour-arrow mo-point-'+tourElement.pointToSide+'">'+
                                '<i style="color:#ffffff;position: relative;" ' +
                                    'class=" mo-dashicons dashicons dashicons-arrow-'+tourElement.pointToSide+'"></i>'+
                            '</div>'+
                            '<div class="mo-tour-content-area mo-point-'+tourElement.pointToSide+'">'+
                                '<div class="mo-tour-title">'+tourElement.titleHTML+'</div>'+
                                '<div class="mo-tour-content">'+tourElement.contentHTML+'</div>'+
                                '<div id="tour_svg"><img '+(tourElement.img ? '':'hidden')+' src="'+tourElement.img+'" style="margin:auto;" alt=""></div>'+
                                '<div class="mo-tour-button-area flex flex-1 gap-mo-6"></div>'
                                +'<div hidden class="mo-tour-card-bottom"></div>' +
                            '</div>' +
                        '</div>';

    let nextButton  =   '<input type="button"  class="mowc-button inverted mo-tour-primary-btn" value="'+tourElement.buttonText+'">';
    let skipButton  =   '<input type="button"  class="mowc-button secondary mo-skip-btn" style="margin-left:30px; margin-right:30px;" value="Skip Tour">';

    $mo("#moblock").empty();
    $mo(card).insertAfter('#moblock');

    $mo('.mo-tour-button-area').append(skipButton);
    $mo('.mo-tour-button-area').append(nextButton); //Will keep true always

    // Emphasised shadow When not pointing to any element and placed in the center
    if(tourElement.pointToSide=='' || tourElement.pointToSide=='center') {
        $mo('.mo-card').attr('style', 'box-shadow:0px 0px 0px 3px #979393');
    }

    // When poiniting to any element, calculate the position of the card
    if(tourElement.targetE) {
        getPointerPosition(tourElement.targetE, tourElement.pointToSide);
    }

    // On Next button clicked, create and display next card if exist else Save tour option
    $mo('.mo-tour-primary-btn').click( function(){
        $mo('.mo-target-index').removeClass('mo-target-index');
        $mo('.mo-card').remove();
            pointerNumber+=1;
            if(moTour.tourData[pointerNumber]){
                createCard(pointerNumber);
            } else {
                resetTour();
                tourComplete();
            }
    });

    // On Skip button click, Reset the tour and remove the overlay and existing card.
    $mo(".mo-skip-btn").click( function() {
        $mo('.mo-target-index').removeClass('mo-target-index');
        $mo('.mo-card').remove();
        resetTour();
        tourComplete();
    });

}

/**
 * This function calculates the Top and Left position for the card to be added
 * w.r.t. the target element. getBoundingClientRect() function returns top, bottom, left,
 * right, height and width of an element.
 * @param targetE     -   Target element to which card points
 * @param pointToSide -   The direction to which card points
 */
function getPointerPosition(targetE,pointToSide) {
    let targetDimentions = document.getElementById(targetE).getBoundingClientRect();
    let cardDimentions   = document.getElementById('mo-card').getBoundingClientRect();
    let finalLeft,finalTop;

    switch(pointToSide) {
        case 'up'    :
                        finalLeft   =   targetDimentions.left + (targetDimentions.width - cardDimentions.width)/2 ;
                        finalTop    =   targetDimentions.top + targetDimentions.height + 5;
                        break;
        case 'down'  :
                        finalLeft   =   targetDimentions.left + (targetDimentions.width - cardDimentions.width)/2 ;
                        finalTop    =   targetDimentions.top - cardDimentions.height ;
                        break;
        case 'left'  :
                        finalLeft   =   targetDimentions.left + targetDimentions.width;
                        finalTop    =   targetDimentions.top + (targetDimentions.height - cardDimentions.height)/2 ;
                        break;
        case 'right' :
                        finalLeft   =   targetDimentions.left - cardDimentions.width;
                        finalTop    =   targetDimentions.top + (targetDimentions.height - cardDimentions.height)/2 ;
                        break;

    }
    // To adjust if card goes out of screen
    if(finalTop<0)  {
        $mo('.mo-tour-arrow>i').css('top','calc(50% - 0.6em + '+finalTop+'px)');
        finalTop=0;
    }
    //Adding the calculated position to the card as css property
    $mo('.mo-card').css({
        'top':(finalTop+$mo(window).scrollTop()-25),
        'left':(finalLeft+$mo(window).scrollLeft()-180),
        'margin-top':'0','margin-left':'0','position':'absolute'
    });

    // To get the target Element over the Overlay and highlight
    $mo('#'+targetE).addClass('mo-target-index');
    // Scroll to the target element

    document.getElementById(targetE).scrollIntoView({
        behavior: 'smooth',
        block: 'center',
        inline: 'center'
    });

}

/**
 * Removes the backdrop i.e. the overlay and cards, Resets the pointer number to zero
 */
function resetTour() {
    pointerNumber=0;
    $mo('#moblock').hide();
}

/**
 * When the last element is reached, save a tourTaken variable in BD.
 * So that the tour doesn't start automatically next time.
 */
function tourComplete() {
    $mo.ajax({
        url: moTour.siteURL,
        type: "POST",
        data: {
            doneTour    :true,
            pageID      :moTour.pageID,
            security    :moTour.tnonce,
            action      :moTour.ajaxAction,
        },
        crossDomain: !0,dataType: "json",
    });
}


