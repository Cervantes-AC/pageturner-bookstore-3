# AI Report Generation - Testing Guide

## Quick Start

### Step 1: Access the Admin Panel
1. Navigate to `http://localhost/activity%206/pageturner-bookstore/admin`
2. Login with admin credentials
3. Go to **AI Reports** section

### Step 2: Generate a Test Report

#### Option A: Sales Report (Recommended for Testing)
1. Click **Create Report**
2. Select **Report Type**: "Sales"
3. Select **Period**: "This Month"
4. Select **Format**: "Concise"
5. Click **Generate**

#### Option B: Inventory Report
1. Click **Create Report**
2. Select **Report Type**: "Inventory"
3. Select **Format**: "Detailed"
4. Click **Generate**

#### Option C: Overview Report
1. Click **Create Report**
2. Select **Report Type**: "Overview"
3. Select **Period**: "This Year"
4. Click **Generate**

### Step 3: Verify Results

The report should display:

✅ **Title** - Professional report title  
✅ **Executive Summary** - 2-3 paragraph overview  
✅ **Insights** - Multiple findings with sections and status  
✅ **Recommendations** - Actionable items with priorities  
✅ **Conclusion** - Forward-looking perspective  
✅ **Data Reference** - Raw data used for analysis  

### Step 4: Check Logs

View detailed processing logs:

```bash
# On Windows (PowerShell)
Get-Content "storage/logs/laravel.log" -Tail 50

# Or check the file directly
storage/logs/laravel.log
```

Look for entries like:
```
[2026-05-16 10:30:45] local.INFO: Retrieved data for report {"report_id":1,"data_keys":["sales_summary","inventory_summary"],"data_size":2345}
[2026-05-16 10:30:47] local.INFO: Response parsed {"report_id":1,"has_summary":true,"insights_count":4,"recommendations_count":3}
```

## Troubleshooting

### Issue: Report shows only data reference, no insights

**Solution 1: Check AI Provider**
- Verify API key is set in `.env`
- Check provider is enabled in `config/ai.php`
- Ensure you haven't exceeded rate limits

**Solution 2: Check Logs**
```bash
# Look for parsing errors
grep -i "failed to parse" storage/logs/laravel.log
grep -i "ai provider" storage/logs/laravel.log
```

**Solution 3: Test with Different Provider**
1. Go to report creation
2. Select a different AI model (if available)
3. Try generating again

### Issue: Report generation times out

**Solution 1: Use Async Mode**
1. When creating report, check "Generate Asynchronously"
2. Report will be queued and processed in background
3. Check back in a few seconds

**Solution 2: Check Queue**
```bash
# Verify queue is running
php artisan queue:work
```

**Solution 3: Increase Timeout**
Edit `app/Jobs/GenerateAIReport.php`:
```php
public int $timeout = 600; // Increase from 300 to 600 seconds
```

### Issue: "All AI providers are unavailable"

**Solution 1: Check API Keys**
```bash
# Verify .env file has API keys
cat .env | grep -i "API_KEY"
```

**Solution 2: Check Provider Configuration**
```bash
# Review config/ai.php
cat config/ai.php | grep -A 5 "providers"
```

**Solution 3: Test Provider Directly**
```bash
# Test Groq API
curl -X POST https://api.groq.com/openai/v1/chat/completions \
  -H "Authorization: Bearer YOUR_KEY" \
  -H "Content-Type: application/json" \
  -d '{"model":"llama-3.3-70b-versatile","messages":[{"role":"user","content":"test"}]}'
```

## Performance Testing

### Test 1: Single Report Generation
```
Expected Time: 2-5 seconds
Expected Tokens: 800-1500
Expected Cost: $0.00 (Groq free tier)
```

### Test 2: Multiple Concurrent Reports
```
Generate 5 reports simultaneously
Expected Time: 5-10 seconds total
Expected Success Rate: 95%+
```

### Test 3: Large Dataset Report
```
Generate "Overview" report with all data
Expected Time: 3-8 seconds
Expected Tokens: 2000-3000
Expected Insights: 4-6 findings
```

## Data Validation

### Check Retrieved Data
1. Generate a report
2. View the report details
3. Scroll to "Raw Data Reference" section
4. Verify data includes:
   - Sales summary (revenue, orders, AOV)
   - Inventory summary (stock, valuation)
   - User summary (counts by role)
   - Review summary (ratings)
   - Trends (monthly data)

### Check Parsed Insights
1. View report details
2. Check "Insights" section
3. Each insight should have:
   - Section heading
   - Detailed content with numbers
   - Status (positive/warning/critical)

### Check Recommendations
1. View report details
2. Check "Recommendations" section
3. Each recommendation should have:
   - Specific action
   - Rationale
   - Priority (high/medium/low)

## Advanced Testing

### Test JSON Extraction
1. Generate a report
2. Check `ai_raw_response` in database:
```sql
SELECT ai_raw_response FROM ai_reports WHERE id = 1;
```
3. Verify response is valid JSON or contains JSON block

### Test Fallback Mechanism
1. Temporarily disable primary provider in `.env`
2. Generate a report
3. Check logs to verify fallback provider was used
4. Verify report still generated successfully

### Test Error Handling
1. Set invalid API key
2. Generate a report
3. Verify error message is clear
4. Check logs for detailed error information

## Monitoring

### Daily Checks
- [ ] Generate at least one report
- [ ] Verify insights are displayed
- [ ] Check logs for errors
- [ ] Monitor token usage

### Weekly Checks
- [ ] Test with different report types
- [ ] Test with different time periods
- [ ] Verify cost tracking is accurate
- [ ] Check for any API errors

### Monthly Checks
- [ ] Review usage statistics
- [ ] Analyze cost trends
- [ ] Check provider performance
- [ ] Update documentation if needed

## Success Criteria

✅ Report generates in < 10 seconds  
✅ All sections are populated (title, summary, insights, recommendations)  
✅ Insights include specific data points and numbers  
✅ Recommendations are actionable and prioritized  
✅ No errors in logs  
✅ Cost tracking is accurate  
✅ Fallback works when primary provider fails  

## Common Test Cases

### Test Case 1: Basic Sales Report
```
Type: Sales
Period: This Month
Format: Concise
Expected: 3-4 insights about revenue, orders, AOV
```

### Test Case 2: Detailed Inventory Report
```
Type: Inventory
Format: Detailed
Expected: 5-6 insights about stock levels, valuation, low stock
```

### Test Case 3: User Analytics
```
Type: Users
Period: This Year
Expected: Insights about user growth, roles, verification
```

### Test Case 4: Bestsellers Report
```
Type: Bestsellers
Period: Last 3 Months
Expected: Top 10 books with sales quantities
```

### Test Case 5: Low Stock Alerts
```
Type: Alerts
Expected: Books with stock < 10 with reorder recommendations
```

## Reporting Issues

When reporting issues, include:

1. **Report ID**: Found in URL or report details
2. **Report Type**: What type of report was generated
3. **Timestamp**: When the report was generated
4. **Error Message**: Any error displayed
5. **Logs**: Relevant entries from `storage/logs/laravel.log`
6. **Expected vs Actual**: What you expected vs what you got

Example:
```
Report ID: 42
Type: Sales Overview
Time: 2026-05-16 10:30:00
Issue: Only data reference shown, no insights
Logs: [attached]
Expected: 4-5 insights with specific numbers
Actual: Empty insights array
```

---

**Last Updated**: May 2026  
**Status**: Ready for Testing
