import { createContext, useState, useEffect, useCallback } from 'react';
import axios from 'axios';
import { toast } from 'react-toastify';

export const UserContext = createContext({
    user: null,
    authStatus: false,
    updateUser: () => {},
    setAuthStatus: () => {},
    logout: () => {},
    loadUserProfile: () => {}
});

const UserProvider = ({ children }) => {
    const [user, setUser] = useState(null);
    const [authStatus, setAuthStatus] = useState(() => !!localStorage.getItem('authToken'));

    const updateUser = useCallback((newData) => {
        setUser(prev => ({ ...prev, ...newData }));
        if (newData) localStorage.setItem('user', JSON.stringify(newData));
    }, []);

    const logout = useCallback(() => {
        localStorage.removeItem('authToken');
        localStorage.removeItem('user');
        setAuthStatus(false);
        setUser(null);
    }, []);

    const loadUserProfile = useCallback(async () => {
        try {
            const token = localStorage.getItem('authToken');
            if (!token) return;
            console.log('Loading user profile with token:', token);

            const response = await axios.get(`${process.env.REACT_APP_API_URL}/profile`, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });
            console.log('Profile loaded:', response.data);
            updateUser(response.data.data);
            setAuthStatus(true);
        } catch (error) {
            if (error.response?.status === 401) {
                logout();
                toast.error('Session expired. Please log in again');
            } else {
                toast.error(error.response?.data?.message || 'Failed to load user profile');
                console.error('Profile load error:', error);
            }
        }
    }, [updateUser, logout]);

    useEffect(() => {
        const token = localStorage.getItem('authToken');
        if (token) {
            setAuthStatus(true);
            loadUserProfile();
        }
    }, [loadUserProfile]);

    return (
        <UserContext.Provider
            value={{
                user,
                authStatus,
                updateUser,
                setAuthStatus,
                logout,
                loadUserProfile
            }}
        >
            {children}
        </UserContext.Provider>
    );
};

export { UserProvider };
