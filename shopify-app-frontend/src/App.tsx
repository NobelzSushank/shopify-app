// // import { useState } from 'react'
// // import reactLogo from './assets/react.svg'
// // import viteLogo from '/vite.svg'
// // import './App.css'

// // function App() {
// //   const [count, setCount] = useState(0)

// //   return (
// //     <>
// //       <div>
// //         <a href="https://vite.dev" target="_blank">
// //           <img src={viteLogo} className="logo" alt="Vite logo" />
// //         </a>
// //         <a href="https://react.dev" target="_blank">
// //           <img src={reactLogo} className="logo react" alt="React logo" />
// //         </a>
// //       </div>
// //       <h1>Vite + React</h1>
// //       <div className="card">
// //         <button onClick={() => setCount((count) => count + 1)}>
// //           count is {count}
// //         </button>
// //         <p>
// //           Edit <code>src/App.tsx</code> and save to test HMR
// //         </p>
// //       </div>
// //       <p className="read-the-docs">
// //         Click on the Vite and React logos to learn more
// //       </p>
// //     </>
// //   )
// // }

// // export default App




// import React from 'react';
// import {BrowserRouter, Routes, Route, Navigate} from 'react-router-dom';
// import {Provider as AppBridgeProvider} from '@shopify/app-bridge-react';
// import {Page} from '@shopify/polaris';
// import DashboardPage from './pages/DashboardPage';
// import ProductsPage from './pages/ProductsPage';

// const App = () => {
//   const shopOrigin = new URLSearchParams(window.location.search).get('shop') || '';
//   const host = new URLSearchParams(window.location.search).get('host') || '';

//   // const appBridgeConfig = {
//   //   apiKey: import.meta.env.VITE_SHOPIFY_API_KEY,
//   //   host,
//   //   forceRedirect: true,
//   // };
//   const appBridgeConfig = {
//     apiKey: 'bcec7adae2d1f7a6dcfb069c84ec96c4',
//     host,
//     forceRedirect: true,
//   };

//   return (
//     <AppBridgeProvider config={appBridgeConfig}>
//       <BrowserRouter>
//         <Page>
//           <Routes>
//             <Route path="/" element={<Navigate to="/dashboard" />} />
//             <Route path="/dashboard" element={<DashboardPage />} />
//             <Route path="/products" element={<ProductsPage />} />
//           </Routes>
//         </Page>
//       </BrowserRouter>
//     </AppBridgeProvider>
//   );
// };

// export default App;



import React from 'react';
import {BrowserRouter, Routes, Route, Navigate} from 'react-router-dom';
import {Frame, Page} from '@shopify/polaris';
// import {useAppBridge} from '@shopify/app-bridge-react'; // v4 hook
import DashboardPage from './pages/DashboardPage';
import ProductsPage from './pages/ProductsPage';

const EnsureHost: React.FC<{children: React.ReactNode}> = ({children}) => {
  // App Bridge v4 reads config from HTML; we just ensure the URL has host
  const host = new URLSearchParams(window.location.search).get('host') || '';
  if (!host) {
    return (
      <div style={{padding: 16, fontFamily: 'system-ui'}}>
        <h3>Missing <code>host</code> parameter</h3>
        <p>Open this app from Shopify Admin so the URL contains <code>?host=...</code> and <code>&shop=...</code>.</p>
      </div>
    );
  }
  return <>{children}</>;
};

const App: React.FC = () => {
  // Example: using App Bridge v4 from React
  // const shopify = useAppBridge(); // available after the script+meta are loaded
  // You can use shopify.toast.show('Hello') etc. when needed.

  // You can also read shop for your own logic if needed
  // const shop = new URLSearchParams(window.location.search).get('shop') || '';

  return (
    <BrowserRouter>
      <EnsureHost>
        <Frame>
          <Routes>
            <Route path="/" element={<Navigate to="/dashboard" />} />
            <Route path="/dashboard" element={
              <Page title="Dashboard">
                <DashboardPage />
              </Page>
            } />
            <Route path="/products" element={
              <Page title="Products">
                <ProductsPage />
              </Page>
            } />
          </Routes>
        </Frame>
      </EnsureHost>
    </BrowserRouter>
  );
};

export default App;
