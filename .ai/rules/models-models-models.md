---
paths:
  - 'app/Models/Member.php,app/Models/MemberPayment.php,app/Models/Shelter.php'
---

# Models Models Models

## Members (sócios): joia + quotas, per-shelter numbering, arrears rules
Member (MultiShelterTrait + Blameable + SoftDeletes) has a per-shelter member_number assigned in booted() creating as max(withTrashed, without shelter scope)+1, so numbers are never reused; an explicit number is kept (for imports). joining_fee (joia) is one-time and may be 0 (never owed then); membership_fee + membership_fee_frequency (monthly/quarterly/semiannual/yearly) is the recurring quota. Shelter holds the same three columns as defaults to pre-fill new members. MemberPayment.type is joining_fee (no period, start/end_date null) or membership_fee (period start_date..end_date). Member::isInArrears(): only 'active' members; true if owesJoiningFee() or membership_fee > 0 and no fee period covers today (feesPaidUntil() = max end_date).
