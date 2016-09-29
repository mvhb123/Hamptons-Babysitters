//
//  ModifyJobViewController.h
//  SitterApp
//
//  Created by Vikram gour on 22/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"

@interface ModifyJobViewController : AppBaseViewController
{

    __weak IBOutlet UILabel *lbl_jobNumberValue;
    __weak IBOutlet UITextField *txt_startJobValue;
    __weak IBOutlet UITextField *txt_endJobValue;
    __weak IBOutlet UITextField *txt_childrenCount;
    __weak IBOutlet UITextView *txt_aboutJob;
    __weak IBOutlet UIView *view_mainBg;
    __weak IBOutlet UIView *view_datePicker;
    __weak IBOutlet UIButton *btn_modifyJob;
    __weak IBOutlet UIButton *btn_addChild;
    __weak IBOutlet UIDatePicker *datePicker;
    __weak IBOutlet UILabel *lbl_jobNumber;
    __weak IBOutlet UILabel *lbl_startDate;
    __weak IBOutlet UILabel *lbl_endDate;
    __weak IBOutlet UILabel *lbl_childCount;
    __weak IBOutlet UILabel *lbl_noteAboutJob;
    
    NSDictionary *dict_jobData;
    int selectedIndex;
    //Constraint
    
}
@property(nonatomic,strong)NSDictionary *dict_jobData;
@property(nonatomic,weak)Sitter *sitterInfo;
@property(assign)int indexPath,jobTypeFlag;
@property(nonatomic,weak)JobList *jobList;
@property(nonatomic,strong)NSString *additionalChildId;
@property(nonatomic,strong)NSString *isExpired;

- (IBAction)onClicked_addChild:(id)sender;
- (IBAction)onClicked_modifyJob:(id)sender;
- (IBAction)onClick_StartEndDate:(id)sender;
- (IBAction)onClickedDateDonebutton:(id)sender;
- (IBAction)onClickedDateCancelbutton:(id)sender;
@end
