//
//  RequestSitterViewController.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 09/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"
#import "Children.h"
#import "VerticallyAlignedLabel.h"
@interface RequestSitterViewController : AppBaseViewController
{

    __weak IBOutlet UIImageView *img_previous;
    __weak IBOutlet UIImageView *img_next;
    __weak IBOutlet UIButton *btn_sitterRequirement;
    __weak IBOutlet UIButton *btn_bookIt;
    __weak IBOutlet UIButton *btn_addChild;
    __weak IBOutlet VerticallyAlignedLabel *lbl_specialInstructions;
    __weak IBOutlet UILabel *lbl_zip;
    __weak IBOutlet UILabel *lbl_state;
    __weak IBOutlet UILabel *lbl_city;
    __weak IBOutlet UILabel *lbl_streetAddress;
    __weak IBOutlet UILabel *lbl_crossStreet;
    __weak IBOutlet UILabel *lbl_hotelName;
    __weak IBOutlet UILabel *lbl_localAddressHeading;
    __weak IBOutlet UILabel *lbl_endDateTime;
    __weak IBOutlet UILabel *lbl_startDateTime;
    __weak IBOutlet UILabel *lbl_dateTimeHeading;
    __weak IBOutlet UILabel *lbl_childMessage;
    __weak IBOutlet UILabel *lbl_selectChildren;
    __weak IBOutlet UIPickerView *picker_view;
    IBOutlet UIView *view_pickerView;
    __weak IBOutlet UIButton *btn_stateList;
    __weak IBOutlet UITableView *tbl_stateList;
    __weak IBOutlet UITextField *txt_zip;
    __weak IBOutlet UITextField *txt_state;
    __weak IBOutlet UITextField *txt_city;
    __weak IBOutlet UITextField *txt_streetAddress;
    __weak IBOutlet UITextField *txt_crossStreet;
    __weak IBOutlet UITextField *txt_hotelName;
    __weak IBOutlet UIView *view_bookChildren;
    __weak IBOutlet UIButton *btn_localAddress;
    __weak IBOutlet UIButton *btn_alternateAddress;
    IBOutlet UIView *view_bottomView;
    IBOutlet UIView *view_alternateAddress;
     Children *childrenInfo;
    __weak IBOutlet UITextView *txtView_specialInstructions;
    __weak IBOutlet UITextField *txt_endDate;
    __weak IBOutlet UITextField *txt_startDate;
    __weak IBOutlet VerticallyAlignedLabel *lbl_localAddress;
    __weak IBOutlet UILabel *lbl_alternateAddress;
    IBOutlet UIView *view_datePicker;
    __weak IBOutlet UIDatePicker *datePicker;
    __weak IBOutlet UIButton *btn_specialNeed;
    __weak IBOutlet UILabel *lbl_specialNeed;
    __weak IBOutlet UITextView *txtView_specialNeed;
    __weak IBOutlet NSLayoutConstraint *viewSpecialInsTopConstraint;
    __weak IBOutlet UILabel *lbl_jobLocationHeading;
    
    
    int checkSelected;
    NSInteger CheckTagValue;
    NSMutableArray *array_childCount;
    NSMutableArray *array_childRecord;
    NSMutableArray *array_childData;
    UILabel *lbl_childName;
    AsyncImageView *view_childProfile;
    UIImageView *img_checked;
    NSMutableArray *array_statelist;
    NSMutableArray *array_states;
    NSString *str_stateId;
    NSString *str_stateName;
    NSString *str_addressId;
    NSMutableDictionary *dict_alternateAddress;
     int checkDropState;
    float contentHight;
    int checkView;
    CGSize ScrollContentOffset;
    NSString *str_btnTag;
    NSDate *startTime;
    NSDate *endTime;
    int t;
    float buttomViewHeight;
    __weak IBOutlet NSLayoutConstraint *consForchildmsg;
    __weak IBOutlet NSLayoutConstraint *consForJobdetailView;
    
    

   
}
@property(nonatomic,assign) Parent *parentInfo;
@property(nonatomic,strong) NSMutableArray *array_Preferences;
@property(nonatomic,strong) NSMutableDictionary *dict_parentRecord;
@property(weak, nonatomic) IBOutlet UIView *view_children;
@property(weak, nonatomic) IBOutlet UIScrollView *scroll_view;
@property(weak, nonatomic) IBOutlet UIScrollView *second_scroll;
- (IBAction)onClickBookSitterrequirement:(id)sender;
- (IBAction)onClickBookforPayment:(id)sender;
- (IBAction)onClickAddChild:(id)sender;
- (IBAction)onClickDoneDate:(id)sender;
- (IBAction)onClickCancleDate:(id)sender;
- (IBAction)onClickChooseDate:(id)sender;
- (IBAction)onClickAlternateAddress:(id)sender;
- (IBAction)onClickStateList:(id)sender;
- (IBAction)onClickSpecialNeed:(id)sender;


@end
