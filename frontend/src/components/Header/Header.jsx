import { useState, useContext, useRef, useEffect } from 'react';
import { Link, NavLink, useNavigate } from 'react-router-dom';
import { FiMenu, FiX, FiUser, FiLogOut } from 'react-icons/fi';
import { UserContext } from '../../context/UserContext';
import styles from './Header.module.css';

const Header = () => {
    const [isMenuOpen, setIsMenuOpen] = useState(false);
    const [isDropdownOpen, setIsDropdownOpen] = useState(false);
    const { user, authStatus, logout } = useContext(UserContext);
    const dropdownRef = useRef(null);
    const navigate = useNavigate();

    const navItems = [
        { name: 'Page 1', path: '/page-1' },
        { name: 'Page 2', path: '/page-2' },
        { name: 'Page 3', path: '/page-3' },
    ];

    useEffect(() => {
        const handleClickOutside = (event) => {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
                setIsDropdownOpen(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const handleLogout = () => {
        logout();
        navigate('/');
    };

    return (
        <header className={styles.header}>
            <div className={styles.container}>
                <Link to="/" className={styles.logo}>
                    <img src="/logo192.png" alt="devway" />
                </Link>

                <nav className={`${styles.nav} ${isMenuOpen ? styles.active : ''}`}>
                    {navItems.map((item) => (
                        <NavLink
                            key={item.path}
                            to={item.path}
                            className={({ isActive }) =>
                                `${styles.navLink} ${isActive ? styles.active : ''}`
                            }
                            onClick={() => setIsMenuOpen(false)}
                        >
                            {item.name}
                        </NavLink>
                    ))}

                    {authStatus && user ? (
                        <div className={styles.profileWrapper} ref={dropdownRef}>
                            <button
                                className={styles.avatarButton}
                                onClick={() => setIsDropdownOpen(!isDropdownOpen)}
                            >
                                <div className={styles.avatar}>
                                    <img
                                        src={user?.avatarUrl || '/default-avatar.png'}
                                        alt="User Avatar"
                                        onError={(e) => {
                                            e.target.src = '/default-avatar.png';
                                        }}
                                    />
                                </div>
                            </button>

                            <div className={`${styles.dropdown} ${isDropdownOpen ? styles.active : ''}`}>
                                <Link
                                    to="/profile"
                                    className={styles.dropdownItem}
                                    onClick={() => setIsDropdownOpen(false)}
                                >
                                    <FiUser className={styles.dropdownIcon} />
                                    Profile
                                </Link>
                                <Link
                                    className={styles.dropdownItem}
                                    onClick={handleLogout}
                                    to="/"
                                >
                                    <FiLogOut className={styles.dropdownIcon} />
                                    Log Out
                                </Link>
                            </div>
                        </div>
                    ) : (
                        <div className={styles.authButtons}>
                            <Link to="/login" className={styles.login}>
                                Sign In
                            </Link>
                            <Link to="/register" className={styles.register}>
                                Get Started
                            </Link>
                        </div>
                    )}
                </nav>

                <button
                    className={styles.menuToggle}
                    onClick={() => setIsMenuOpen(!isMenuOpen)}
                >
                    {isMenuOpen ? <FiX /> : <FiMenu />}
                </button>
            </div>
        </header>
    );
};

export default Header;