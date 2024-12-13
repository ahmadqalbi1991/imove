<script type="module">
    // Import the functions you need from the SDKs you need
    import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.1/firebase-app.js";
    import { getAnalytics } from "https://www.gstatic.com/firebasejs/11.0.1/firebase-analytics.js";
    import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/11.0.1/firebase-messaging.js";

    // Your web app's Firebase configuration
    const firebaseConfig = {
      apiKey: "AIzaSyBwDiLbeiSHa4z_4FNP3pyuyVONJ8lq4Ww",
      authDomain: "ahmed-4f9e4.firebaseapp.com",
      projectId: "ahmed-4f9e4",
      storageBucket: "ahmed-4f9e4.appspot.com",
      messagingSenderId: "448157536333",
      appId: "1:448157536333:web:ce289156282eb130010c04",
      databaseURL: "https://ahmed-4f9e4.firebaseio.com",
      measurementId: "G-0ER2JL43NK"
    };

    // Initialize Firebase
    const app = initializeApp(firebaseConfig);
    const analytics = getAnalytics(app);

    // Initialize Firebase Messaging
    const messaging = getMessaging(app);












// Request permission to get FCM token
async function requestFcmToken() {
  try {
    const permission = await Notification.requestPermission();
    if (permission === 'granted') {
      const fcmToken = await getToken(messaging, {
        vapidKey: 'BEsUyzOkWMvCmza59rWrzceSYxCl9x6X6j6Sf9YG5NAeewfVIAEIS6gSDXTnXZHcK288F-Us9GCgFXjwFk2Y-iA', // Replace with your Firebase VAPID key
      });
      if (fcmToken) {
        console.log('FCM Token:', fcmToken);
        // Send token to server or use it as needed
      } else {
        console.log('No registration token available.');
      }
    } else {
      console.log('Notification permission denied');
    }
  } catch (error) {
    console.error('An error occurred while retrieving token:', error);
  }
}

// Listen for messages while the app is open
onMessage(messaging, (payload) => {
  console.log('Message received:', payload);
});


if ('serviceWorker' in navigator) {
  navigator.serviceWorker
    .register('/firebase-messaging-sw.js')
    .then((registration) => {
      console.log('Service Worker registered with scope:', registration.scope);
    })
    .catch((error) => {
      console.error('Service Worker registration failed:', error);
    });
}


// Call the function to request the token
requestFcmToken();






    // // Request permission to get the token
    // async function requestPermission() {
    //     try {
    //         await Notification.requestPermission();
    //         console.log("Notification permission granted.");
    //         // Get the FCM token
    //         const token = await getToken(messaging, { vapidKey: "BND_q0uv4LZ0BR-Ph-gDCXQnWnGZLJ-72qxIqsoXgnNQqw-Wa9VS7ngDcK5LIujHsjwR1OuXAdjPoRB8xB3sXww" });
    //         console.log("FCM Token:", token);
    //     } catch (error) {
    //         console.error("Unable to get permission to notify.", error);
    //     }
    // }

    // // Call the function to request permission and get the token
    // requestPermission();

    // // Listen for incoming messages
    // onMessage(messaging, (payload) => {
    //     console.log("Message received. ", payload);
    //     // Process the message payload here
    // });
</script>
