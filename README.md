# iskoprint

## PayPal Sandbox Setup

To use PayPal sandbox for testing payments:

1. **Create a PayPal Developer Account**
   - Go to https://developer.paypal.com/
   - Sign in with your PayPal account or create a new one

2. **Create a Sandbox App**
   - Navigate to Dashboard → My Apps & Credentials
   - Click "Create App" under Sandbox
   - Give your app a name (e.g., "iskOPrint Sandbox")
   - Select "Merchant" as the app type
   - Click "Create App"

3. **Get Your Sandbox Client ID**
   - After creating the app, you'll see your Client ID
   - Copy the Client ID (it will look like: `AeA1QIZXiflr1_-...`)

4. **Configure the Application**
   - Open `config/paypal_config.php`
   - Replace `YOUR_SANDBOX_CLIENT_ID_HERE` with your actual sandbox Client ID
   - Ensure `PAYPAL_ENVIRONMENT` is set to `'sandbox'`

5. **Test with Sandbox Accounts**
   - In PayPal Developer Dashboard, go to Accounts → Sandbox → Accounts
   - Use the default sandbox personal account or create test accounts
   - Use these test accounts to complete test payments

**Note:** For production, create a Live app and update the credentials in `config/paypal_config.php`, then set `PAYPAL_ENVIRONMENT` to `'production'`.