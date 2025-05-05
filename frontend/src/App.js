import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { UserProvider } from './context/UserContext';
import LoginForm from './features/auth/components/LoginForm/LoginForm';
import RegisterForm from './features/auth/components/RegisterForm/RegisterForm';
import ForgotPasswordForm from './features/auth/components/ForgotPasswordForm/ForgotPasswordForm';
import ResetPasswordForm from './features/auth/components/ResetPasswordForm/ResetPasswordForm';
import Header from "./components/Header/Header";
import Home from './features/home/HomePage/HomePage';
import ProfileEditPage from "./features/profile/ProfileEditPage/ProfileEditPage";
import ProfileOverviewPage from "./features/profile/ProfileOverviewPage/ProfileOverviewPage";
import './shared/styles/global.css';
import ProtectedRoute from './components/ProtectedRoute/ProtectedRoute';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import './shared/styles/toast-custom.css';
import GoogleCallback from "./features/auth/components/GoogleCallback/GoogleCallback";
import VerifyEmailPrompt from "./features/auth/components/VerifyEmailPrompt/VerifyEmailPrompt";
import VerifyEmail from "./features/auth/components/VefifyEmail/VerifyEmail";

function App() {
    return (
        <UserProvider>
            <Router>
                <Header />
                <Routes>
                    <Route path="/" element={<Home />} />
                    <Route path="/login" element={<LoginForm />} />
                    <Route path="/register" element={<RegisterForm />} />
                    <Route path="/google/callback" element={<GoogleCallback />} />
                    <Route path="/forgot-password" element={<ForgotPasswordForm />} />
                    <Route path="/reset-password" element={<ResetPasswordForm />} />
                    <Route path="/verify-email/:token" element={<VerifyEmail />} />
                    <Route path="/verify-email-prompt" element={<VerifyEmailPrompt />} />
                    <Route path="/profile" element={
                        <ProtectedRoute>
                            <ProfileOverviewPage />
                        </ProtectedRoute>
                    } />
                    <Route path="/profile/edit" element={
                        <ProtectedRoute>
                            <ProfileEditPage />
                        </ProtectedRoute>
                    } />
                </Routes>
                <ToastContainer
                    position="bottom-right"
                    autoClose={3000}
                    hideProgressBar={true}
                    newestOnTop={true}
                    closeOnClick
                    rtl={false}
                    pauseOnFocusLoss
                    draggable
                    pauseOnHover
                    toastClassName="custom-toast"
                    bodyClassName="custom-toast-body"
                />
            </Router>
        </UserProvider>
    );
}

export default App;
