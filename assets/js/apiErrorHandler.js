/**
 * Global handler for API and Fetch-related errors.
 */
const ApiErrorHandler = {
    /**
     * Parses various error types into user-friendly messages.
     * @param {Error} error - The error object caught in the catch block.
     * @returns {string} A localized/friendly error message.
     */
    parse(error) {
        // Keep detailed logs in the console for developer debugging
        console.error('DEBUG - API Error Details:', error);

        // 1. Handle JSON Parsing Errors 
        // (Usually happens when PHP returns a Warning/Fatal Error in HTML format instead of JSON)
        if (error instanceof SyntaxError && error.message.includes('JSON')) {
            return "Invalid data format received from the server (Data Parse Error).";
        }

        // 2. Handle manually thrown HTTP errors (e.g., 404, 500, 403)
        if (error.message.includes('HTTP error')) {
            const statusCode = error.message.split(':').pop().trim();
            return `Server responded with an error (Status Code: ${statusCode}). Please try again later.`;
        }

        // 3. Handle Network Connectivity or CORS issues
        if (error.message === 'Failed to fetch' || error.message.includes('NetworkError')) {
            return "Unable to connect to the server. Please check your internet connection.";
        }

        // 4. Fallback for any other unexpected errors
        return error.message || "An unexpected error occurred. Please contact the system administrator.";
    }
};