import axios from 'axios';
import {Link, useNavigate} from 'react-router-dom';
import AuthLayout from '../AuthLayout/AuthLayout';
import Button from '../../../../shared/ui/Button/Button';
import Input from '../../../../shared/ui/Input/Input';
import Spinner from "../../../../shared/ui/Spinner/Spinner";
import { UserContext } from '../../../../context/UserContext';
import styles from './LoginForm.module.css';
import {useContext, useState} from "react";
import {toast} from "react-toastify";
import {FiEye, FiEyeOff} from "react-icons/fi";

const LoginForm = () => {
    const navigate = useNavigate();
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [showPassword, setShowPassword] = useState(false);
    const { loadUserProfile } = useContext(UserContext);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsLoading(true);

        try {
            const response = await axios.post(
                `${process.env.REACT_APP_API_URL}/auth/login`,
                { email, password }
            );

            if (
                response.data &&
                response.data.data &&
                response.data.data.original &&
                response.data.data.original.message === 'Email not verified'
            ) {
                toast.error('Your email is not verified. Please verify your email.');
                navigate('/verify-email-prompt', { state: { email } });
                return;
            }

            if (response.data.success) {
                const { authToken } = response.data.data.original;
                localStorage.setItem('authToken', authToken);
                await loadUserProfile();
                navigate('/profile');
            }
        } catch (error) {
            if (error.response?.status === 401) {
                toast.error('Invalid email or password');
            } else {
                toast.error(error.response?.data?.message || 'Login failed');
            }
        } finally {
            setIsLoading(false);
        }
    };

    const handleGoogleLogin = () => {
        window.location.href = `${process.env.REACT_APP_API_URL}/auth/google/redirect`;
    };

    return (
        <AuthLayout title="Welcome Back">
            <form onSubmit={handleSubmit}>
                <div>
                    <Input
                        type="email"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        label="Email address"
                        placeholder="Enter your email"
                        required
                    />
                </div>

                <div className={styles.passwordInputContainer}>
                    <Input
                        type={showPassword ? 'text' : 'password'}
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        label="Password"
                        placeholder="••••••••"
                        required
                    />
                    <div
                        onClick={() => setShowPassword(!showPassword)}
                        className={styles.passwordToggleIcon}
                    >
                        {showPassword ? <FiEyeOff size={20} /> : <FiEye size={20} />}
                    </div>
                </div>

                <Button type="submit" disabled={isLoading}>
                    {isLoading ? (
                        <div className={styles.buttonContent}>
                            <Spinner size="sm" />
                            <span>Processing...</span>
                        </div>
                    ) : 'Sign In'}
                </Button>

                <div className={styles.divider}>
                    <span>or continue with</span>
                </div>

                <Button
                    type="button"
                    className={styles.googleBtn}
                    onClick={handleGoogleLogin}
                    variant="secondary"
                >
                    <img
                        src="/google.svg"
                        alt="Google"
                        className={styles.googleIcon}
                    />
                    Sign in with Google
                </Button>

                <div className={styles.links}>
                    <Link to="/register">Don't have an account? Sign Up</Link>
                    <Link to="/forgot-password">Forgot Password?</Link>
                </div>
            </form>
        </AuthLayout>
    );
};

export default LoginForm;