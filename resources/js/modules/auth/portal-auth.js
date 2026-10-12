import axios from 'axios';
import { getCurrentUser, setCurrentUser } from './auth.js';

const AUTH_TOKEN_KEY = 'smoovehub_auth_token';
const AUTH_FINGERPRINT_KEY = 'smoovehub_device_fp';

function getDeviceFingerprint() {
    let fingerprint = localStorage.getItem(AUTH_FINGERPRINT_KEY);

    if (!fingerprint) {
        fingerprint = crypto.randomUUID();
        localStorage.setItem(AUTH_FINGERPRINT_KEY, fingerprint);
    }

    return fingerprint;
}

function getDeviceMeta() {
    const platform = navigator.platform || null;
    const browser = navigator.userAgent?.slice(0, 80) || null;

    return {
        device_name: platform ? `${platform} Browser` : 'Web Browser',
        device_fingerprint: getDeviceFingerprint(),
        platform,
        browser,
    };
}

function setAuthToken(token) {
    localStorage.setItem(AUTH_TOKEN_KEY, token);
    axios.defaults.headers.common.Authorization = `Bearer ${token}`;
}

function clearAuthToken() {
    localStorage.removeItem(AUTH_TOKEN_KEY);
    delete axios.defaults.headers.common.Authorization;
}

export function userHasAnyRole(user, roles = []) {
    if (!user || !roles.length) {
        return false;
    }

    const userRoles = Array.isArray(user.roles) ? user.roles : [];
    const primary = user.primary_role;

    return roles.some((role) => userRoles.includes(role) || primary === role);
}

export async function portalLogin(email, password) {
    const response = await axios.post('/api/v1/auth/login', {
        email,
        password,
        ...getDeviceMeta(),
    });

    const data = response.data?.data ?? {};
    const token = data.token;
    const user = data.user;

    if (!response.data?.status || !token || !user) {
        throw new Error(response.data?.message || 'Sign in failed.');
    }

    setAuthToken(token);
    setCurrentUser(user);

    return user;
}

export async function portalLogout() {
    try {
        if (getCurrentUser() || localStorage.getItem(AUTH_TOKEN_KEY)) {
            await axios.post('/api/v1/auth/logout');
        }
    } catch {
        // Clear local session even if API fails.
    } finally {
        clearAuthToken();
        setCurrentUser(null);
    }
}

export async function restorePortalUser() {
    const token = localStorage.getItem(AUTH_TOKEN_KEY);

    if (!token) {
        return getCurrentUser();
    }

    axios.defaults.headers.common.Authorization = `Bearer ${token}`;

    try {
        const response = await axios.get('/api/v1/auth/me');
        const user = response.data?.data?.user ?? null;

        if (user) {
            setCurrentUser(user);
        }

        return user;
    } catch {
        clearAuthToken();
        setCurrentUser(null);
        return null;
    }
}

export function portalAuthErrorMessage(error) {
    return error.response?.data?.message
        ?? error.response?.data?.errors?.email?.[0]
        ?? error.response?.data?.errors?.password?.[0]
        ?? error.message
        ?? 'Unable to sign in. Please try again.';
}
