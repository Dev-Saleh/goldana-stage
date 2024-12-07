async function startSession() {
  try {
    const response = await fetch(
      'https://api-capital.backend-capital.com/api/v1/session',
      {
        method: 'POST',
        headers: {
          'X-CAP-API-KEY': 'vQ5hjpmakUVD0N3N',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          identifier: 'dvlpr.saleh@gmail.com',
          password: 'Cc-0537221210',
        }),
      }
    );
    if (response.ok) console.log('session started');

    if (!response.ok) {
      throw new Error('Network response was not ok');
    }

    const cst = response.headers.get('cst');
    const securityToken = response.headers.get('x-security-token');

    return { cst, securityToken };
  } catch (error) {
    console.error('Error starting session:', error);
    throw error;
  }
}
function injection_liveprice_fetching() {
  try {
    const updateProductPrices = () => {
      const elements = document.querySelectorAll(
        '.wc-block-components-product-price:not([data-updated])'
      );
      elements.forEach((element) => {
        element.innerHTML = `<p>يتم جلب السعر...</p>`;
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

/*------------------------------------------------------------------------------------------------------------------*/

function renderProductPrice(elements, livePrice_24, calculatedValue, color) {
  elements.forEach((element) => {
    const weight = parseFloat(element.getAttribute('data-product-weight')) || 1;
    const manufacturingFees =
      parseFloat(element.getAttribute('data-product-manufacturing-fees')) || 1;
    const goldCarat = parseInt(element.getAttribute('data-product-gold-carat'));

    // console.log("Raw gold carat attribute:", element.getAttribute('data-product-gold-carat'));
    // console.log("Element being processed:", element);
    // console.log(goldCarat);

    switch (goldCarat) {
      case 18: {
        const livePrice_18 = livePrice_24 * 0.75;
        const value_18 = weight * (manufacturingFees + livePrice_18) * 1.15;

        // Adjusting calculatedValue based on the color
        calculatedValue = color === '#10B981' ? value_18 + 2 : value_18 - 2;
        break;
      }

      case 21: {
        const livePrice_21 = livePrice_24 * 0.875;
        const value_21 = weight * (manufacturingFees + livePrice_21) * 1.15;

        // Adjusting calculatedValue based on the color
        calculatedValue = color === '#10B981' ? value_21 + 2 : value_21 - 2;
        break;
      }

      case 24: {
        calculatedValue = livePrice_24 * weight;
        break;
      }

      default:
        calculatedValue = 'بيانات ناقصه';
        // console.warn('Invalid gold carat value:', goldCarat);
        break;
    }

    const displayValue =
      typeof calculatedValue === 'number'
        ? calculatedValue.toFixed(2) // Format the value to 2 decimal places
        : calculatedValue; // Use the string as-is if it's not a number

    // Insert the calculated value into the HTML and apply the dynamic color
    element.innerHTML = `
             <style>
                /* Container for price and indicator */
                .price-indicator {
                    display: flex;
                    align-items: center;
                    font-family: 'Arial', sans-serif;
                    font-size: 1.3rem;
                
                    // justify-content: start;
                }

                /* Price text */
                .pricee {
                    font-weight: bold !important;
                      font-size: 1.25rem; !important;
                    margin: 0px !important;
                  color: ${color} !important;
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

              .curencyy{
                margin-right: 4px;
                font-size : 1.2rem
              }

            
            </style>
            
            <div class="price-indicator">
             <span class="arrow" id="arrow">-</span>
                <span class="pricee">${displayValue}</span>
               <span class="curencyy" id="curency">ر.س</span>
            </div>
          `;

    const arrowElement = element.querySelector('#arrow');

    if (color === '#10B981') {
      arrowElement.innerHTML = '▲';
      arrowElement.classList.remove('down');
      arrowElement.classList.add('up');
    } else if (color === '#F43F5E') {
      arrowElement.innerHTML = '▼';
      arrowElement.classList.remove('up');
      arrowElement.classList.add('down');
    } else {
      arrowElement.innerHTML = '-';
      arrowElement.classList.remove('up', 'down');
    }
  });
}

/*-------------------------------------------------------------------------------------------------------------------*/
let ws; // Declare WebSocket variable outside the function for global scope.

async function initiateWebSocketConnection() {
  try {
    // Close any existing WebSocket connection
    if (ws && ws.readyState !== WebSocket.CLOSED) {
      console.log('Closing existing WebSocket connection...');
      ws.close();
    }

    let cst = localStorage.getItem('CST');
    let securityToken = localStorage.getItem('TOKEN');

    if (!cst || !securityToken) {
      console.log('Start new session...');
      const sessionData = await startSession();
      cst = sessionData.cst;
      securityToken = sessionData.securityToken;
      localStorage.setItem('CST', cst);
      localStorage.setItem('TOKEN', securityToken);
    }

    // Initialize the WebSocket connection
    ws = new WebSocket('wss://api-streaming-capital.backend-capital.com/connect');

    ws.onopen = () => {
      if (!localStorage.getItem('CST') || !localStorage.getItem('TOKEN')) {
        console.log('No CST and TOKEN. Start new session from onOpen...');
      } else {
        console.log('CST & TOKEN are in storage');
        const subscriptionMessage = {
          destination: 'marketData.subscribe',
          correlationId: '100',
          cst,
          securityToken,
          payload: { epics: ['GOLD'] },
        };
        ws.send(JSON.stringify(subscriptionMessage));
        console.log('WebSocket connection opened');
      }
    };

    let prev = 0;

    ws.onmessage = (event) => {
      try {
        const data = JSON.parse(event.data);
        if (data.status === 'OK') {
          const livePrice_24 = (data.payload.bid * 121.5) / 1000;
          console.log('WebSocket message received:', livePrice_24);

          const difference = livePrice_24 - (prev + 0.001);
          const color = difference < 0 ? '#F43F5E' : '#10B981';
          prev = livePrice_24;

          const elements = document.querySelectorAll('#livePriceEl');
          renderProductPrice(elements, livePrice_24, 1, color);
        } else if (data.payload.errorCode === 'error.invalid.session.token') {
          localStorage.removeItem('CST');
          localStorage.removeItem('TOKEN');
        }
      } catch (e) {
        console.error('Error parsing WebSocket message:', e);
      }
    };

    ws.onerror = (error) => {
      console.error('WebSocket error:', error);
    };

    ws.onclose = () => {
      console.log('WebSocket connection closed');
    };

    // Add event to close WebSocket connection when page is unloaded
    window.onbeforeunload = () => {
      if (ws && ws.readyState === WebSocket.OPEN) {
        console.log('Page is unloading, closing WebSocket connection...');
        ws.close();
      }
    };

  } catch (error) {
    console.error('Failed to start session or establish WebSocket connection:', error);
  }
}


function injection_top_bar(){
     
     const pageWrapper = document.querySelector("body .content-page .sidebar-position-without .row .content div section");
     console.log(pageWrapper);
    if(pageWrapper)
    {
         const marquee = document.createElement("div");
          marquee.id = "announcement";
          marquee.innerHTML = `
            
          <span> 🤗 سعر البورصه متوقف يتم تثبيت اخر سعر للبورصه</span>
        
        
        <style>
          #announcement {
            background-color: yellow;
            color: #ef4444;
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
            margin: 5px 0px;
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
document.addEventListener('DOMContentLoaded', () => {
        
        const todayUTC = new Date();
        const dayOfWeekUTC = todayUTC.getUTCDay(); // Returns 0 (Sunday) to 6 (Saturday)
        const hoursUTC = todayUTC.getUTCHours();
        const minutesUTC = todayUTC.getUTCMinutes();
        
        // Helper to check if current time is within trading hours
        function isMarketOpen(day, hours, minutes) {
          // Market closes on Saturday at 00:59 UTC and opens on Monday at 02:00 UTC
          if (day === 0) return false; // Sunday - market is closed
          if (day === 6 && (hours > 0 || (hours === 0 && minutes > 59))) return false; // After Saturday 00:59 UTC
          if (day === 1 && (hours < 2)) return false; // Before Monday 02:00 UTC
          return true;
        }
        
        if (isMarketOpen(dayOfWeekUTC, hoursUTC, minutesUTC)) {
            injection_liveprice_fetching();
          initiateWebSocketConnection(); // Market is open
          console.log('Market is open. WebSocket connection initiated.');
        } else {
          injection_top_bar(); // Market is closed
          console.log('Market is closed. Showing the top bar.');
        }

});