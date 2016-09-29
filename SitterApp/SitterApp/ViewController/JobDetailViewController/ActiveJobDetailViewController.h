//
//  ActiveJobDetsilViewController.h
//  SitterApp
//
//  Created by Vikram gour on 16/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"
#import "ModifyJobViewController.h"
#import <MessageUI/MessageUI.h>

@interface ActiveJobDetailViewController : AppBaseViewController<MFMailComposeViewControllerDelegate>
{
    __weak IBOutlet UIView *view_mainBG;
    NSDictionary *dict_ChildData;
    __weak IBOutlet UIScrollView *scroll_View;
    __weak IBOutlet UIButton *btn_modifyJob;
    __weak IBOutlet UIButton *btn_cancelJob;
    __weak IBOutlet UIView *view_jobDetail;
    __weak IBOutlet UIView *view_parentDetail;
    __weak IBOutlet UILabel *lbl_headingChildDetail;
    __weak IBOutlet UIView *view_otherContactDetail;
    __weak IBOutlet UIView *view_specialNotes;

    __weak IBOutlet UILabel *lbl_jobNumberValue;
    __weak IBOutlet UILabel *lbl_startDateValue;
    __weak IBOutlet UILabel *lbl_endDateValue;
    __weak IBOutlet UILabel *lbl_childCountValue;
    __weak IBOutlet VerticallyAlignedLabel *lbl_AddressValue;
    __weak IBOutlet UILabel *lbl_parentNameValue;
    __weak IBOutlet UILabel *lbl_parentPhone1Value;
    __weak IBOutlet UILabel *lbl_parentPhone2Value;
    __weak IBOutlet UILabel *lbl_ParentEmailValue;
    __weak IBOutlet UILabel *lbl_guardianNameValue;
    __weak IBOutlet UILabel *lbl_guardianPhone1Value;
    __weak IBOutlet UILabel *lbl_guardianPhone2Value;
    __weak IBOutlet UILabel *lbl_emergencyContactNameValue;
    __weak IBOutlet UILabel *lbl_relationshipValue;
    __weak IBOutlet UILabel *lbl_emergencyContactPhone1Value;
    __weak IBOutlet VerticallyAlignedLabel *lbl_specialNotesValue;
    
    __weak IBOutlet UILabel *lbl_jobNumber;
    __weak IBOutlet UILabel *lbl_startDate;
    __weak IBOutlet UILabel *lbl_endDate;
    __weak IBOutlet UILabel *lbl_childCount;
    __weak IBOutlet UILabel *lbl_Address;
    __weak IBOutlet UILabel *lbl_parentName;
    __weak IBOutlet UILabel *lbl_parentPhone1;
    __weak IBOutlet UILabel *lbl_parentPhone2;
    __weak IBOutlet UILabel *lbl_ParentEmail;
    __weak IBOutlet UILabel *lbl_guardianName;
    __weak IBOutlet UILabel *lbl_guardianPhone1;
    __weak IBOutlet UILabel *lbl_guardianPhone2;
    __weak IBOutlet UILabel *lbl_emergencyContactName;
    __weak IBOutlet UILabel *lbl_relationship;
    __weak IBOutlet UILabel *lbl_emergencyContactPhone1;
    __weak IBOutlet UILabel *lbl_specialNotes;
    __weak IBOutlet UILabel *lbl_OtherContactHeading;
    __weak IBOutlet UILabel *lbl_area;
    __weak IBOutlet UILabel *lbl_areaValue;
    __weak IBOutlet UILabel *lbl_emContactName;
    __weak IBOutlet UILabel *lbl_specialIns;
    __weak IBOutlet VerticallyAlignedLabel *lbl_specialInsValue;

    
    
    //For next Previous
    int pageNo;
    int totalCount;
    int currentPage;
    __weak IBOutlet UIButton *btn_previous;
    __weak IBOutlet UIButton *btn_next;
    NSMutableArray *arrChildData;
    __weak IBOutlet NSLayoutConstraint *consForSpecialNeedHeight;
    __weak IBOutlet NSLayoutConstraint *consForContactHeight;
    __weak IBOutlet NSLayoutConstraint *consForEmergencyContact;
    
    __weak IBOutlet NSLayoutConstraint *consForSpecialNeed;
    __weak IBOutlet NSLayoutConstraint *consForcancelbtn;
    __weak IBOutlet UIView *view_bottom;
    float scrollContentSize;
    
}
@property(assign)int indexPath,jobTypeFlag;
@property(nonatomic,strong)NSMutableArray *array_Jobs;
@property(nonatomic,assign)JobList *jobList;
@property(nonatomic,weak)Sitter *sitterInfo;
;
@property(nonatomic,assign)int jobId;
-(void)getJobDetail:(int)jobID;

- (IBAction)onClicked_modifyJob:(id)sender;
- (IBAction)onClicked_cancelJobs:(id)sender;
- (IBAction)onClickNext:(id)sender;
- (IBAction)onClickPrevious:(id)sender;
@end
