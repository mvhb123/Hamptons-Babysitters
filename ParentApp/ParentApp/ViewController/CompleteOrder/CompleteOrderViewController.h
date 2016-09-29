//
//  CompleteOrderViewController.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 17/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"
#import "VerticallyAlignedLabel.h"

@interface CompleteOrderViewController : AppBaseViewController
{
    __weak IBOutlet UILabel *lbl_availableCredits;
    __weak IBOutlet UILabel *lbl_availbaleCreditsValue;
    __weak IBOutlet UITextView *txtViewSpecialInstructions;
    
    __weak IBOutlet UITextView *txtView_specialNeeds;
    __weak IBOutlet UIButton *btn_completeOrder;
    __weak IBOutlet UILabel *lbl_bottomLabelMessage;
    __weak IBOutlet UILabel *lbl_sitterReqHeading;
    __weak IBOutlet UILabel *lbl_specialInsHeading;
    __weak IBOutlet UIButton *btn_buyMultipleCredits;
    __weak IBOutlet UILabel *lbl_bookingFeeHeading;
    __weak IBOutlet UILabel *lbl_babySitterFeeHeading;
    __weak IBOutlet UILabel *lbl_feeSuumaryHeading;
    __weak IBOutlet UILabel *lbl_jobAddressHeading;
    __weak IBOutlet UILabel *lbl_endDTHeading;
    __weak IBOutlet UILabel *lbl_startDTHeading;
    __weak IBOutlet UILabel *lbl_dateTimeHeading;
    __weak IBOutlet UILabel *lbl_selectedChildren;
    Children *childrenInfo;
    __weak IBOutlet UITableView *tbl_sitterRequirement;
    __weak IBOutlet UIView *view_addJobDetail;
    __weak IBOutlet UIScrollView *view_scrollView;
    __weak IBOutlet UILabel *lbl_otherPrefrences;
    __weak IBOutlet UILabel *lbl_langauges;
    __weak IBOutlet UILabel *lbl_specialInstruction;
    __weak IBOutlet UILabel *lbl_bookingFees;
    __weak IBOutlet UILabel *lbl_babySitterFees;
    __weak IBOutlet UILabel *lbl_SpecialNeedsHeading;
  
    __weak IBOutlet VerticallyAlignedLabel *llbl_jobAddress;
    __weak IBOutlet UILabel *lbl_endDateTime;
    __weak IBOutlet UILabel *lbl_startDateTime;
    NSMutableArray *array_selectedPreference;
    NSMutableArray *array_otherPreference;
    NSString *str_preferenceId;
    NSString *str_childId;
    NSMutableArray *array_lanaguge;
    NSMutableArray *array_preferences;
     float contentHight;
    int checkValue;
    
    __weak IBOutlet NSLayoutConstraint *consForlblSpecialNeed;
    __weak IBOutlet NSLayoutConstraint *consFortxtSpecialNeed;
    __weak IBOutlet NSLayoutConstraint *consForlblSpecialIns;
    __weak IBOutlet NSLayoutConstraint *consFortxtSpecialIns;
    
    __weak IBOutlet NSLayoutConstraint *consForlblSitterPref;
    __weak IBOutlet NSLayoutConstraint *consFortblSitterPref;
    
    __weak IBOutlet UILabel *lbl_totalChargeHeading;
    __weak IBOutlet UILabel *lbl_totalCharge;
    __weak IBOutlet UIView *view_feeSumarry;
    
    
}
@property(strong,nonatomic)NSMutableDictionary* dict_addJobRequirement;
- (IBAction)onClickBookingCredits:(id)sender;
- (IBAction)onClickCompleteOrder:(id)sender;

@end
