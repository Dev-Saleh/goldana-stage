export function injection_top_bar(){
     
    //  const pageWrapper = document.querySelector("body .content-page .sidebar-position-without .row .content div section");
     const pageWrapper = document.querySelector("body .template-container .template-content .page-wrapper .etheme-elementor-header-sticky");
  
    if(pageWrapper)
    {
         const marquee = document.createElement("div");
          marquee.id = "announcement";
          marquee.innerHTML = `
            
          <span> تتوقف سعر البورصه يومي السبت الاحد والساعه 01:00 صباحا </span>
        
        
        <style>
          #announcement {
            background-color: #ef4444;
            color: #fef2f2;
            padding: 5px;
            font-weight: bold;
            font-size: 14px;
            overflow: hidden;
            white-space: nowrap;
            position: relative;
            top: 0;
            left: 0;
            width: 100%;
            // z-index: ;
            margin: 0px 0px;
          }
        
          #announcement span {
            display: inline-block;
            padding-left: 100%;
            animation: marquee 14s linear infinite;
          }
        
          @keyframes marquee {
            from {
              transform: translateX(-50%);
            }
            to {
              transform: translateX(50%);
            }
          }
        </style>

  
  `;
   pageWrapper.insertAdjacentElement("afterend", marquee);
    }

}

/*-------------------------------------------------------------------------------------------------------------------*/

export function injection_label_liveprice_fetching() {
  try {
    const updateProductPrices = () => {
      const elements = document.querySelectorAll(
        '.wc-block-components-product-price:not([data-updated])'
      );
      elements.forEach((element) => {
        element.innerHTML = `<p>يتم جلب السعر المباشر..</p>`;
        element.setAttribute('data-updated', 'true');
      });
    };

    updateProductPrices();

    const observer = new MutationObserver((mutationsList) => {
      for (const mutation of mutationsList) {
        if (mutation.type === 'childList') {
          updateProductPrices();
        }
      }
    });

    const productContainer = document.querySelector('.etheme-product-grid');
    if (productContainer) {
      observer.observe(productContainer, { childList: true });
    }
  } catch (error) {
    console.error('Error starting session:', error);
  }
}

/*-------------------------------------------------------------------------------------------------------------------*/

export function injectionFixedLivePrice() {
    let calculatedFixedPrice = 0;
    const fixedElements = document.querySelectorAll('#livePriceEl');
    fixedElements.forEach((element) => {
        const weight = parseFloat(element.getAttribute('data-product-weight')) || 1;
        const manufacturingFees = parseFloat(element.getAttribute('data-product-manufacturing-fees')) || 1;
        const goldCarat = parseInt(element.getAttribute('data-product-gold-carat'));
        const fixed_price_24 = parseFloat(element.getAttribute('data-fixed-price')) || 0;

        switch (goldCarat) {
            case 18: {
                const livePrice_18 = fixed_price_24 * 0.75;
                calculatedFixedPrice = weight * (manufacturingFees + livePrice_18) * 1.15;
                console.log('18', calculatedFixedPrice);
                break;
            }

            case 21: {
                const livePrice_21 = fixed_price_24 * 0.875;
                calculatedFixedPrice = weight * (manufacturingFees + livePrice_21) * 1.15;
                console.log('21', calculatedFixedPrice);
                break;
            }

            case 24: {
                calculatedFixedPrice = fixed_price_24 * weight;
                break;
            }

            default: {
                calculatedFixedPrice = 0; // Default to 0 for invalid data
                console.warn('Invalid gold carat value or missing data:', goldCarat);
                break;
            }
        }

        // Ensure the calculatedFixedPrice is a number
        const displayPrice = calculatedFixedPrice.toFixed(2);

        // Insert the calculated value into the HTML
        element.innerHTML = `
            <style>
                /* Container for price and indicator */
                .price-indicator {
                    display: flex;
                    align-items: center;
                    font-family: 'Arial', sans-serif;
                    font-size: 1.3rem;
                }

                /* Price text */
                .pricee {
                    font-weight: bold !important;
                    font-size: 1.4rem !important;
                    margin: 0px !important;
                    color: red !important;
                }

                .arrow {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1rem;
                    transition: transform 0.3s ease, color 0.3s ease;
                }

                .up {
                    color: #10b981;
                }

                .down {
                    color: #ef4444;
                }

                .curencyy {
                    margin-right: 4px;
                    font-size: 1.2rem;
                }
            </style>
            
            <div class="price-indicator">
                <span class="arrow down" id="arrow">▼</span>
                <span class="pricee">${displayPrice}</span>
                <span class="curencyy" id="curency">ر.س</span>
            </div>
        `;
    });
}



