//
//  JobDetailViewController.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 16/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"
#import "SMCoreComponent.h"
#import <MessageUI/MessageUI.h>
#import "NumberFormatter.h"
#import "VerticallyAlignedLabel.h"

@interface JobDetailViewController : AppBaseViewController<MFMailComposeViewControllerDelegate>
{
    Children *childrenInfo;
    
    __weak IBOutlet UIButton *btn_previous;
    __weak IBOutlet UIButton *btn_next;
    __weak IBOutlet UIScrollView *scrollView_kidsList;
    __weak IBOutlet UILabel *lbl_kidsList;
    __weak IBOutlet UILabel *lbl_viewStatus;
    __weak IBOutlet UILabel *lbl_viewEndDate;
    __weak IBOutlet UILabel *lbl_viewStartDate;
    __weak IBOutlet UILabel *lbl_viewJobNumber;
    __weak IBOutlet UIView *view_jobDetail;
    __weak IBOutlet UILabel *lbl_sitterDetail;
    __weak IBOutlet UILabel *lbl_babySitterPhoneHeader;
    __weak IBOutlet UILabel *lbl_babySitterEmailHeader;
    __weak IBOutlet UILabel *lbl_babySitterNameHeader;
    __weak IBOutlet UITableView *tbl_kidsList;
    __weak IBOutlet UILabel *lbl_status;
    __weak IBOutlet UILabel *lbl_sitterName;
    __weak IBOutlet UILabel *lbl_sitterMobileNumber;
    __weak IBOutlet UILabel *lbl_sitterEmail;
    __weak IBOutlet UIButton *btn_sitterName;
    __weak IBOutlet AsyncImageView *view_kidsProfile;
    __weak IBOutlet UILabel *lbl_sex;
    __weak IBOutlet UILabel *lbl_age;
    __weak IBOutlet UILabel *lbl_kidsName;
    __weak IBOutlet UILabel *lbl_jobNumber;
    __weak IBOutlet UILabel *lbl_startDate;
    __weak IBOutlet UILabel *lbl_enddate;
    __weak IBOutlet UILabel *lbl_kidsListHeading;
    __weak IBOutlet AsyncImageView *img_sitterProfile;
    __weak IBOutlet VerticallyAlignedLabel
 *lbl_jobAddress;
    
    __weak IBOutlet UILabel *lbl_address;
    
    NSMutableArray *array_childList;
    float contentHight;
     int j;
    int pageNo;
    int totalCount;
    int currentPage;
    
    
}
@property (nonatomic ,strong) NumberFormatter *numFormatter;
@property (nonatomic)int checkJobType;
@property (weak, nonatomic) IBOutlet UIButton *btn_cancel;
@property (nonatomic,strong)NSMutableDictionary *dict_jobsDetail;
@property (nonatomic,assign)Parent *parentInfo;
@property (nonatomic,strong)JobList *jobInfo;
@property (nonatomic,strong)NSString *str_adminContact;

- (IBAction)onClickCancelJOb:(id)sender;
- (IBAction)onClickSitterProfile:(id)sender;
- (IBAction)onClickNext:(id)sender;
- (IBAction)onClickPrevious:(id)sender;

@end
