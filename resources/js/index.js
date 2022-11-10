import ReactDOM from 'react-dom'
import { createRoot } from 'react-dom/client';
import App from './app';
import store from './store';
import { Provider } from "react-redux";
import { Routes, Route, useNavigate, BrowserRouter } from 'react-router-dom'
import 'antd/dist/antd.css';

const container = document.getElementById('root');
const root = createRoot(container); // createRoot(container!) if you use TypeScript
root.render(
    <BrowserRouter>
        <Provider store={store}>
            <Routes>
                <Route path='/*' element={<App />} />
               
            </Routes>
        </Provider>
    </BrowserRouter>,
);