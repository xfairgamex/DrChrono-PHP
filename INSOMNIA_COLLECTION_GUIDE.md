# DrChrono API - Insomnia Test Collection Guide

This guide explains how to use the comprehensive Insomnia collection (`insomnia-collection.json`) to test every feature of the DrChrono PHP SDK.

## 📋 Collection Overview

The collection includes **100+ API requests** organized into **20 categories**:

1. **Authentication** - OAuth2 flows (authorize, token exchange, refresh, revoke)
2. **Users & Current User** - User information
3. **Patient Management** - CRUD operations, summary, CCDA, patient portal
4. **Appointments** - Scheduling, management, profiles, templates
5. **Doctors & Providers** - Provider information
6. **Offices & Exam Rooms** - Location management
7. **Clinical Notes** - Documentation, templates, field types
8. **Documents** - Upload, download, metadata management
9. **Laboratory** - Lab orders, results, tests, documents
10. **Patient Clinical Data** - Allergies, medications, problems, vitals, immunizations
11. **Prescriptions** - Prescription management
12. **Insurance & Eligibility** - Insurance plans, eligibility verification
13. **Billing & Payments** - Line items, transactions, billing profiles
14. **Tasks & Workflow** - Task management, templates, categories
15. **Messaging & Communication** - Internal and patient messaging
16. **Care Plans & Preventive Care** - Care plan management
17. **Inventory & Vaccines** - Vaccine inventory tracking
18. **Custom Fields & Demographics** - Custom patient/appointment fields
19. **Procedures & Consent** - Medical procedures, consent forms
20. **Administrative** - User groups, fee schedules, reminders

## 🚀 Getting Started

### 1. Import the Collection

1. Open **Insomnia** (download from https://insomnia.rest if needed)
2. Click **Application Menu** → **Import/Export** → **Import Data**
3. Select **From File** and choose `insomnia-collection.json`
4. The collection "DrChrono PHP SDK - Complete API Collection" will appear

### 2. Configure Environment Variables

After importing, you'll need to set up your environment variables:

1. Click on the **Base Environment** in the collection
2. Update the following values:

```json
{
  "base_url": "https://app.drchrono.com",
  "access_token": "YOUR_ACCESS_TOKEN_HERE",
  "client_id": "YOUR_CLIENT_ID_HERE",
  "client_secret": "YOUR_CLIENT_SECRET_HERE",
  "redirect_uri": "http://localhost:8080/callback",
  "patient_id": "",
  "appointment_id": "",
  "doctor_id": "",
  "office_id": "",
  "clinical_note_id": "",
  "document_id": "",
  "lab_order_id": "",
  "task_id": ""
}
```

### 3. Get Your API Credentials

#### Option A: Using Existing Access Token

If you already have an access token:
1. Update the `access_token` variable
2. Skip to **Step 4**

#### Option B: OAuth2 Flow (Recommended)

1. **Create a DrChrono Developer Application**:
   - Go to https://app.drchrono.com/api-management/
   - Create a new application
   - Copy your `client_id` and `client_secret`
   - Set redirect URI to `http://localhost:8080/callback`

2. **Use the OAuth2 Requests**:
   - Update `client_id`, `client_secret`, and `redirect_uri` in environment
   - Open **01. Authentication** → **OAuth2 - Get Authorization Code**
   - Copy the URL and open it in your browser
   - Authorize the app and copy the `code` from the redirect URL
   - Open **OAuth2 - Exchange Code for Token** request
   - Replace `AUTHORIZATION_CODE_FROM_STEP_1` with your code
   - Send the request
   - Copy the `access_token` from the response
   - Update your environment's `access_token` variable

### 4. Populate Resource IDs

Many requests require resource IDs (patient_id, doctor_id, etc.). To get these:

1. **Get Current User**:
   - Send: `02. Users & Current User` → `Get Current User`
   - Save any relevant IDs

2. **List Doctors**:
   - Send: `05. Doctors & Providers` → `List Doctors`
   - Copy a `doctor_id` to your environment

3. **List Offices**:
   - Send: `06. Offices & Exam Rooms` → `List Offices`
   - Copy an `office_id` to your environment

4. **List or Create a Patient**:
   - Send: `03. Patient Management` → `List Patients`
   - OR create a new patient with `Create Patient`
   - Copy a `patient_id` to your environment

5. **Repeat for other resources** as needed

## 📖 How to Use Each Request

### Request Parameters

Most requests have **query parameters** that are disabled by default. To use them:

1. Open any request (e.g., `List Patients`)
2. Go to the **Query** tab
3. Enable checkboxes for parameters you want to use
4. Modify values as needed

Example parameters:
- `page` - Pagination page number
- `page_size` - Records per page (max 250)
- `verbose` - Include additional related data
- `doctor` - Filter by doctor ID
- `patient` - Filter by patient ID
- `date` - Filter by specific date
- `date_range` - Filter by date range (YYYY-MM-DD/YYYY-MM-DD)

### Request Body

For POST/PATCH requests, the body is pre-populated with example data:

1. Open any create/update request
2. Go to the **Body** tab
3. Modify the JSON data
4. Replace `{{ _.variable }}` references with actual IDs from your environment

### Using Environment Variables

The collection uses Insomnia's template syntax:

- `{{ _.base_url }}` - Base API URL
- `{{ _.access_token }}` - Your access token
- `{{ _.patient_id }}` - Patient ID
- etc.

These automatically pull from your environment variables.

## 🧪 Testing Workflows

### Example: Complete Patient Workflow

1. **Create Patient**: `03. Patient Management` → `Create Patient`
2. **Update Patient**: Copy ID from response, update environment, then `Update Patient`
3. **Add Allergy**: `10. Patient Clinical Data` → `Create Allergy`
4. **Record Vitals**: `Record Vital Signs`
5. **Create Appointment**: `04. Appointments` → `Create Appointment`
6. **Create Clinical Note**: `07. Clinical Notes` → `Create Clinical Note`

### Example: Billing Workflow

1. **Create Appointment**: Get appointment_id
2. **Add Line Items**: `13. Billing & Payments` → `Create Line Item`
3. **Record Payment**: `Create Transaction`
4. **View Billing Profile**: `List Billing Profiles`

### Example: Lab Order Workflow

1. **List Available Tests**: `09. Laboratory` → `List Lab Tests`
2. **Create Lab Order**: `Create Lab Order`
3. **Check Lab Results**: `List Lab Results`
4. **View Lab Documents**: `List Lab Documents`

## 🔍 Advanced Features

### Pagination

For list endpoints:
```
?page=1&page_size=20
```

Use the `next` URL from responses to get the next page.

### Verbose Mode

Enable `verbose=true` for additional related data:
```
/api/patients?verbose=true
```

**Note**: Verbose mode reduces page size (50 vs 250) and is slower (2-5x).

### Date Filtering

Filter by date or date range:
```
?date=2025-12-12
?date_range=2025-12-01/2025-12-31
?since=2025-01-01
```

### File Upload

For document upload (`08. Documents` → `Upload Document`):

1. Switch body type to **Multipart Form**
2. Add form fields:
   - `patient` - Patient ID
   - `doctor` - Doctor ID
   - `description` - Document description
   - `document` - File (click and select file)

## 📊 Testing Coverage

This collection covers:
- ✅ **64 Resource Classes**
- ✅ **100+ API Endpoints**
- ✅ **All CRUD Operations** (Create, Read, Update, Delete)
- ✅ **OAuth2 Authentication**
- ✅ **Pagination & Filtering**
- ✅ **Verbose Mode**
- ✅ **File Upload**
- ✅ **Query Parameters**

### Endpoint Categories Covered:

| Category | Endpoints | Coverage |
|----------|-----------|----------|
| Patient Management | 7 | ✅ Complete |
| Appointments | 5 | ✅ Complete |
| Clinical Notes | 4 | ✅ Complete |
| Documents | 5 | ✅ Complete |
| Laboratory | 5 | ✅ Complete |
| Patient Data | 6 | ✅ Complete |
| Prescriptions | 2 | ✅ Complete |
| Insurance | 3 | ✅ Complete |
| Billing | 5 | ✅ Complete |
| Tasks | 5 | ✅ Complete |
| Messaging | 3 | ✅ Complete |
| Care Plans | 2 | ✅ Complete |
| Inventory | 2 | ✅ Complete |
| Custom Fields | 3 | ✅ Complete |
| Administrative | 7 | ✅ Complete |

## 🔐 Security Best Practices

1. **Never commit your access tokens** to version control
2. **Use environment variables** for all sensitive data
3. **Rotate tokens regularly** using the refresh token flow
4. **Revoke tokens** when no longer needed
5. **Use HTTPS only** (enforced by base_url)

## 🐛 Troubleshooting

### "Unauthorized" Error
- Check your `access_token` is valid and not expired
- Use the **OAuth2 - Refresh Token** request to get a new token

### "Not Found" Error
- Verify the resource ID exists (patient_id, doctor_id, etc.)
- Check you're using the correct environment variable

### "Validation Error"
- Review the request body for required fields
- Check field formats (dates, IDs, etc.)
- Consult DrChrono API documentation

### Rate Limiting
- DrChrono has rate limits
- The SDK includes automatic retry with exponential backoff
- Wait and retry if you hit rate limits

## 📚 Additional Resources

- **DrChrono API Documentation**: https://app.drchrono.com/api-docs
- **SDK Documentation**: See `README.md`
- **Example Code**: See `examples/` directory
- **Best Practices**: See `BEST_PRACTICES.md`

## 💡 Tips

1. **Start with authentication** - Get your access token first
2. **Populate IDs** - Get resource IDs before testing dependent endpoints
3. **Use verbose mode carefully** - Only when you need related data
4. **Test pagination** - Use small page_size to test pagination logic
5. **Save responses** - Copy IDs from responses to reuse in other requests
6. **Organize tests** - Use Insomnia folders to create custom test suites

## ✅ Quick Validation Checklist

Before running the collection:
- [ ] Imported `insomnia-collection.json` into Insomnia
- [ ] Set `client_id` and `client_secret` in environment
- [ ] Obtained `access_token` via OAuth2 or direct token
- [ ] Retrieved and saved `doctor_id` from List Doctors
- [ ] Retrieved and saved `office_id` from List Offices
- [ ] Created or retrieved `patient_id` for testing
- [ ] Tested authentication with "Get Current User"

You're now ready to test every feature of the DrChrono PHP SDK!

---

**Questions or Issues?**
- Check the SDK's `README.md` for code examples
- Review the DrChrono API documentation
- Test endpoints individually before combining in workflows
